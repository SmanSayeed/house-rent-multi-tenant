<?php

namespace App\Services;

use App\Models\User;
use App\Models\Bill;
use App\Models\TenantAssignment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NotificationEmail;

class EmailNotificationService
{
    /**
     * Send bill created notification
     */
    public function sendBillCreatedNotification(Bill $bill): void
    {
        try {
            $houseOwner = $bill->flat->building->owner;
            $tenant = $bill->flat->activeTenantAssignment?->tenant;

            // Notify house owner
            if ($houseOwner && $houseOwner->email) {
                $this->sendEmail([
                    'to' => $houseOwner->email,
                    'to_name' => $houseOwner->name,
                    'subject' => 'New Bill Created - ' . $bill->title,
                    'template' => 'emails.bill-created',
                    'data' => [
                        'bill' => $bill,
                        'user' => $houseOwner,
                        'user_type' => 'house_owner',
                        'flat' => $bill->flat,
                        'building' => $bill->flat->building,
                        'category' => $bill->category,
                    ]
                ]);
            }

            // Notify tenant
            if ($tenant && $tenant->email) {
                $this->sendEmail([
                    'to' => $tenant->email,
                    'to_name' => $tenant->name,
                    'subject' => 'New Bill - ' . $bill->title,
                    'template' => 'emails.bill-created',
                    'data' => [
                        'bill' => $bill,
                        'user' => $tenant,
                        'user_type' => 'tenant',
                        'flat' => $bill->flat,
                        'building' => $bill->flat->building,
                        'category' => $bill->category,
                    ]
                ]);
            }

            Log::info('Bill created notifications sent', [
                'bill_id' => $bill->id,
                'house_owner_notified' => $houseOwner && $houseOwner->email,
                'tenant_notified' => $tenant && $tenant->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send bill created notifications', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send bill paid notification
     */
    public function sendBillPaidNotification(Bill $bill): void
    {
        try {
            $houseOwner = $bill->flat->building->owner;
            $tenant = $bill->flat->activeTenantAssignment?->tenant;

            // Notify house owner
            if ($houseOwner && $houseOwner->email) {
                $this->sendEmail([
                    'to' => $houseOwner->email,
                    'to_name' => $houseOwner->name,
                    'subject' => 'Bill Paid - ' . $bill->title,
                    'template' => 'emails.bill-paid',
                    'data' => [
                        'bill' => $bill,
                        'user' => $houseOwner,
                        'user_type' => 'house_owner',
                        'flat' => $bill->flat,
                        'building' => $bill->flat->building,
                        'category' => $bill->category,
                    ]
                ]);
            }

            // Notify tenant
            if ($tenant && $tenant->email) {
                $this->sendEmail([
                    'to' => $tenant->email,
                    'to_name' => $tenant->name,
                    'subject' => 'Payment Confirmed - ' . $bill->title,
                    'template' => 'emails.bill-paid',
                    'data' => [
                        'bill' => $bill,
                        'user' => $tenant,
                        'user_type' => 'tenant',
                        'flat' => $bill->flat,
                        'building' => $bill->flat->building,
                        'category' => $bill->category,
                    ]
                ]);
            }

            Log::info('Bill paid notifications sent', [
                'bill_id' => $bill->id,
                'house_owner_notified' => $houseOwner && $houseOwner->email,
                'tenant_notified' => $tenant && $tenant->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send bill paid notifications', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send tenant assigned notification
     */
    public function sendTenantAssignedNotification(TenantAssignment $assignment): void
    {
        try {
            $tenant = $assignment->tenant;
            $houseOwner = $assignment->flat->building->owner;

            // Notify tenant
            if ($tenant && $tenant->email) {
                $this->sendEmail([
                    'to' => $tenant->email,
                    'to_name' => $tenant->name,
                    'subject' => 'Flat Assignment - ' . $assignment->flat->flat_number,
                    'template' => 'emails.tenant-assigned',
                    'data' => [
                        'assignment' => $assignment,
                        'user' => $tenant,
                        'user_type' => 'tenant',
                        'flat' => $assignment->flat,
                        'building' => $assignment->flat->building,
                        'house_owner' => $houseOwner,
                    ]
                ]);
            }

            // Notify house owner
            if ($houseOwner && $houseOwner->email) {
                $this->sendEmail([
                    'to' => $houseOwner->email,
                    'to_name' => $houseOwner->name,
                    'subject' => 'Tenant Assigned - ' . $assignment->flat->flat_number,
                    'template' => 'emails.tenant-assigned',
                    'data' => [
                        'assignment' => $assignment,
                        'user' => $houseOwner,
                        'user_type' => 'house_owner',
                        'flat' => $assignment->flat,
                        'building' => $assignment->flat->building,
                        'tenant' => $tenant,
                    ]
                ]);
            }

            Log::info('Tenant assigned notifications sent', [
                'assignment_id' => $assignment->id,
                'tenant_notified' => $tenant && $tenant->email,
                'house_owner_notified' => $houseOwner && $houseOwner->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send tenant assigned notifications', [
                'assignment_id' => $assignment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send custom notification
     */
    public function sendCustomNotification(array $data): void
    {
        try {
            $this->sendEmail($data);
            
            Log::info('Custom notification sent', [
                'to' => $data['to'],
                'subject' => $data['subject'],
                'template' => $data['template'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send custom notification', [
                'to' => $data['to'],
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send email using the common email template
     */
    private function sendEmail(array $data): void
    {
        Mail::to($data['to'], $data['to_name'] ?? null)
            ->send(new NotificationEmail(
                $data['subject'],
                $data['template'],
                $data['data'] ?? []
            ));
    }
}
