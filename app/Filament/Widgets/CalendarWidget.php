<?php

namespace App\Filament\Widgets;

use App\Enums\Priority;
use App\Models\Meeting;
use App\Models\Sprint;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Guava\Calendar\Actions\CreateAction;
use Guava\Calendar\Widgets\CalendarWidget as BaseCalendarWidget;
use Illuminate\Support\Collection;

class CalendarWidget extends BaseCalendarWidget
{
    protected bool $eventClickEnabled = true;

    protected bool $eventDragEnabled = true;

    protected bool $eventResizeEnabled = true;

    public function getEvents(array $fetchInfo = []): Collection | array
    {
        return collect()
            ->push(...Meeting::query()->get())
            ->push(...Sprint::query()->get())
        ;
    }

    public function getEventContent(): null | string | array
    {
        return [
            Meeting::class => view('components.calendar.events.meeting'),
            Sprint::class => view('components.calendar.events.sprint'),
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                CreateAction::make('createMeeting')
                    ->model(Meeting::class),
                CreateAction::make('createSprint')
                    ->model(Sprint::class),
            ]),
        ];
    }

    public function getEventClickContextMenuActions(): array
    {
        return [
            $this->editAction(),
            $this->deleteAction(),
        ];
    }

    public function getDateClickContextMenuActions(): array
    {
        return [
            CreateAction::make('ctxCreateMeeting')
                ->model(Meeting::class)
                ->mountUsing(function (Form $form, array $arguments) {
                    $date = data_get($arguments, 'dateStr');

                    if ($date) {
                        $form->fill([
                            'starts_at' => Carbon::make($date)->setHour(12),
                            'ends_at' => Carbon::make($date)->setHour(13),
                        ]);
                    }
                }),
        ];
    }

    public function getDateSelectContextMenuActions(): array
    {
        return [
            CreateAction::make('ctxCreateSprint')
                ->model(Sprint::class)
                ->mountUsing(function (Form $form, array $arguments) {
                    $startsAt = data_get($arguments, 'startStr');
                    $endsAt = data_get($arguments, 'endStr');

                    if ($startsAt && $endsAt) {
                        $form->fill([
                            'priority' => Priority::Medium,
                            'starts_at' => Carbon::make($startsAt),
                            'ends_at' => Carbon::make($endsAt),
                        ]);
                    }
                }),
        ];
    }

    public function getSchema(?string $model = null): ?array
    {
        return match ($model) {
            Meeting::class => [
                TextInput::make('title')
                    ->required(),
                RichEditor::make('description'),
                Group::make([
                    DateTimePicker::make('starts_at')
                        ->native(false)
                        ->seconds(false)
                        ->required(),
                    DateTimePicker::make('ends_at')
                        ->native(false)
                        ->seconds(false)
                        ->required(),
                ])->columns(),
                Select::make('users')
                    ->relationship('users', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ],
            Sprint::class => [
                TextInput::make('title')
                    ->required(),
                RichEditor::make('description'),
                Select::make('priority')
                    ->options(Priority::class)
                    ->default(Priority::Medium)
                    ->required(),
                Group::make([
                    DatePicker::make('starts_at')
                        ->native(false)
                        ->required(),
                    DatePicker::make('ends_at')
                        ->native(false)
                        ->required(),
                ])->columns(),
            ]
        };
    }

    public function onEventDrop(array $info = []): bool
    {
        parent::onEventDrop($info);

        if (in_array($this->getModel(), [Meeting::class, Sprint::class])) {
            $record = $this->getRecord();

            if ($delta = data_get($info, 'delta')) {
                $startsAt = $record->starts_at;
                $endsAt = $record->ends_at;
                $startsAt->addSeconds(data_get($delta, 'seconds'));
                $endsAt->addSeconds(data_get($delta, 'seconds'));
                $record->update([
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                ]);

                Notification::make()
                    ->title('Event date moved!')
                    ->success()
                    ->send()
                ;
            }

            return true;
        }

        return false;
    }

    public function onEventResize(array $info = []): bool
    {
        parent::onEventResize($info);

        if ($this->getModel() === Sprint::class) {
            $record = $this->getRecord();
            if ($delta = data_get($info, 'endDelta')) {
                $endsAt = $record->ends_at;
                $endsAt->addSeconds(data_get($delta, 'seconds'));
                $record->update([
                    'ends_at' => $endsAt,
                ]);
            }

            Notification::make()
                ->title('Event duration changed!')
                ->success()
                ->send()
            ;

            return true;

        }

        Notification::make()
            ->title('Duration of this event cannot be changed!')
            ->danger()
            ->send()
        ;

        return false;
    }

    public function authorize($ability, $arguments = [])
    {
        return true;
    }
}
