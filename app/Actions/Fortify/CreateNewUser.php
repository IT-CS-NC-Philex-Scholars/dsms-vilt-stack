<?php

declare(strict_types=1);

namespace App\Actions\Fortify;


use App\Models\Team;
use App\Models\User;
use App\Models\Scholar;
use App\Models\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;


final class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
     public function create(array $input)
         {
             Validator::make($input, [
                 'name' => ['required', 'string', 'max:255'],
                 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                 'password' => $this->passwordRules(),
                 'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
             ])->validate();

             $preQualificationData = session('pre_qualification_data');

             return DB::transaction(function () use ($input, $preQualificationData) {
                 // Create the user
                 $user = User::create([
                     'name' => $preQualificationData ?
                         $preQualificationData['first_name'] . ' ' . $preQualificationData['last_name'] :
                         $input['name'],
                     'email' => $preQualificationData ? $preQualificationData['email'] : $input['email'],
                     'password' => Hash::make($input['password']),
                 ]);

                 $this->createTeam($user);
                 $this->createCustomer($user);

                 // Assign scholar role to user
                 $scholarRole = Role::findOrCreate('scholar');
                 $user->assignRole($scholarRole);

                 // Create scholar profile if pre-qualified
                 if ($preQualificationData) {
                     Scholar::create([
                         'user_id' => $user->id,
                         'first_name' => $preQualificationData['first_name'],
                         'middle_name' => $preQualificationData['middle_name'] ?? null,
                         'last_name' => $preQualificationData['last_name'],
                         'email' => $preQualificationData['email'],
                         'contact_number' => $preQualificationData['contact_number'],
                         'address' => $preQualificationData['address'],
                         'birth_date' => $preQualificationData['birth_date'],
                         'gender' => $preQualificationData['gender'],
                         'status' => 'inactive',
                         'type' => 'scholar',
                         'year_level' => $preQualificationData['year_level'] ?? 1, // Set default to 1 if not provided
                         'course' => $preQualificationData['course'] ?? 'Undeclared', // Set default if not provided
                         'school_id' => $preQualificationData['school_id'] ?? null,
                         'additional_details' => [
                             'current_grade' => $preQualificationData['current_grade'],
                             'enrollment_intent' => $preQualificationData['enrollment_intent'],
                         ],
                     ]);

                     // Create application
                     Application::create([
                         'user_id' => $user->id,
                         'status' => 'incomplete',
                     ]);

                     // Clear the session data
                     session()->forget(['pre_qualification_data', 'is_pre_qualified', 'flash_success_message']);
                 }

                 return $user;
             });

         }

         /**
          * Create a personal team for the user.
          */
         private function createTeam(User $user): void
         {
             $user->ownedTeams()->save(Team::query()->forceCreate([
                 'user_id' => $user->id,
                 'name' => explode(' ', $user->name, 2)[0]."'s Team",
                 'personal_team' => true,
             ]));
         }

         /**
          * Create a billing customer for the user.
          */
         private function createCustomer(User $user): void
         {
             if (! Config::get('cashier.billing_enabled')) {
                 return;
             }

             /** @var Customer $stripeCustomer */
             $stripeCustomer = $user->createOrGetStripeCustomer();

             $user->update([
                 'stripe_id' => $stripeCustomer->id,
             ]);
         }
}
