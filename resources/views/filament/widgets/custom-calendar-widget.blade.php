<x-filament-widgets::widget>
    <x-filament::section>
        <div wire:ignore x-data="{
            init() {
                    if (typeof FullCalendar === 'undefined') {
                        let script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js';
                        script.onload = () => this.initCalendar();
                        document.head.appendChild(script);
                    } else {
                        this.initCalendar();
                    }
                },
                initCalendar() {
                    let calendarEl = this.$refs.calendar;
                    let calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'timeGridWeek',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        slotMinTime: '06:00:00',
                        slotMaxTime: '22:00:00',
                        allDaySlot: false,
                        events: @js($this->fetchEvents()),
                        eventClick: (info) => {
                            info.jsEvent.preventDefault();
                            $wire.mountAction('viewBooking', { record: info.event.id });
                        }
                    });
                    calendar.render();
                }
        }">
            <div x-ref="calendar" class="w-full min-h-[600px] z-10 antialiased"></div>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
