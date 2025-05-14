<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $imei
 * @property int $user_id
 * @property string $model
 * @property string $plate_number
 * @property string $vehicle_type
 * @property string $fuel_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GpsDevice|null $gpsDevice
 * @property-read \App\Models\CustomerVehicleGps|null $gpsRecords
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Installation> $installations
 * @property-read int|null $installations_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\CustomerVehicleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereFuelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereImei($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle wherePlateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereVehicleType($value)
 * @mixin \Eloquent
 */
	class CustomerVehicle extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\CustomerVehicle|null $Vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicleGps newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicleGps newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicleGps query()
 * @mixin \Eloquent
 */
	class CustomerVehicleGps extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CylinderTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class CylinderType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $device_id
 * @property string $latitude
 * @property string $longitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class DeviceLocation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $device_id
 * @property int|null $vehicle_id
 * @property string $activity_status
 * @property string $assignment_status
 * @property int|null $assigned_to
 * @property string|null $assigned_by
 * @property \Illuminate\Support\Carbon|null $assigned_at
 * @property \Illuminate\Support\Carbon|null $unassigned_at
 * @property string|null $unassigned_by
 * @property string|null $unassigned_reason
 * @property string $power_status
 * @property \Illuminate\Support\Carbon|null $power_status_updated_at
 * @property string|null $power_status_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\GpsDeviceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereActivityStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereAssignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereAssignmentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice wherePowerStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice wherePowerStatusNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice wherePowerStatusUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereUnassignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereUnassignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereUnassignedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice withoutTrashed()
 * @mixin \Eloquent
 */
	class GpsDevice extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsPosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsPosition query()
 * @mixin \Eloquent
 */
	class GpsPosition extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $customer_vehicle_id
 * @property int $cylinder_type_id
 * @property string $status
 * @property string $payment_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CustomerVehicle $customerVehicle
 * @property-read \App\Models\CylinderType $cylinderType
 * @property-read \App\Models\Loan|null $loan
 * @method static \Database\Factories\InstallationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereCustomerVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereCylinderTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Installation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $installation_id
 * @property string $loan_type
 * @property string $loan_required_amount
 * @property string $loan_payment_plan
 * @property string|null $loan_start_date
 * @property string|null $loan_end_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $rejection_reason
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanDocument> $documents
 * @property-read int|null $documents_count
 * @property-read mixed $time_to_next_payment
 * @property-read \App\Models\Installation $installation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\LoanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereInstallationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanPaymentPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanRequiredAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereUserId($value)
 * @mixin \Eloquent
 */
	class Loan extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $loan_id
 * @property string $document_type
 * @property string $document_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Loan $loan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class LoanDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $total_installation
 * @property string $down_payment
 * @property string $amount_to_finance
 * @property string $cylinder_capacity
 * @property string $min_price
 * @property string $max_price
 * @property string $payment_plan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereAmountToFinance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereCylinderCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereDownPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereMaxPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereMinPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage wherePaymentPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereTotalInstallation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class LoanPackage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $device_id
 * @property string $latitude
 * @property string $longitude
 * @property string $timestamp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GpsDevice|null $gpsDevice
 * @method static \Database\Factories\LocationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Location extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $loan_id
 * @property int $users_id
 * @property string $payment_date
 * @property string $paid_amount
 * @property string $payment_method
 * @property string $payment_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Loan $loan
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUsersId($value)
 * @mixin \Eloquent
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Post extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $loan_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $due_date
 * @property \Illuminate\Support\Carbon $scheduled_at
 * @property string|null $message
 * @property string $status
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Loan $loan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereUserId($value)
 * @mixin \Eloquent
 */
	class ScheduledReminder extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UdpMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UdpMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UdpMessage query()
 * @mixin \Eloquent
 */
	class UdpMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property string|null $gender
 * @property string|null $dob
 * @property string|null $nida_number
 * @property string|null $address
 * @property string $role
 * @property string $password
 * @property int $verification_code
 * @property string $status
 * @property string|null $coordinates
 * @property int $banned
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $formal_name
 * @property-read \App\Models\GpsDevice|null $gpsDevice
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Loan> $loans
 * @property-read int|null $loans_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerVehicle> $vehicles
 * @property-read int|null $vehicles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNidaNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVerificationCode($value)
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

