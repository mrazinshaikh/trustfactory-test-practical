<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Mail\DailySalesReport;
use Illuminate\Notifications\Notification;

class DailySalesReportNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public array $salesData,
        public array $summary,
        public string $csvPath,
        public Carbon $reportDate,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): DailySalesReport
    {
        return (
            new DailySalesReport(
                $this->salesData,
                $this->summary,
                $this->csvPath,
                $this->reportDate,
            )
        )->to($notifiable->email, $notifiable->name);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'report_date' => $this->reportDate->toDateString(),
            'summary'     => $this->summary,
        ];
    }
}
