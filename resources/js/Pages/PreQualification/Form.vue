<template>
  <web-layout>
    <div class="container py-12 mx-auto">
      <div class="mx-auto max-w-3xl space-y-10">
        <div class="space-y-3 text-center">
          <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">
            Begin Your Scholarship Journey
          </h1>
          <p class="text-lg text-muted-foreground">
            Let's check if you're eligible for our scholarship programs.
            Please fill out the form accurately.
          </p>
        </div>

        <Card class="shadow-lg">
          <CardHeader>
            <div class="flex items-start gap-4">
              <div
                class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary"
              >
                <Icon icon="lucide:file-text" class="h-6 w-6" />
              </div>
              <div>
                <CardTitle class="text-2xl">Pre-Qualification Form</CardTitle>
                <CardDescription class="mt-1">
                  Your accurate information helps us determine your initial
                  eligibility.
                </CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent class="pt-6">
            <form @submit.prevent="submit" class="space-y-8">
              <!-- Personal Information Section -->
              <div class="space-y-6">
                <div class="flex items-center gap-3 border-b pb-3">
                  <Icon
                    icon="lucide:user-circle"
                    class="h-5 w-5 text-primary"
                  />
                  <h3 class="text-lg font-semibold">Personal Information</h3>
                </div>
                <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-3">
                  <div class="space-y-1.5">
                    <Label for="first_name">First Name</Label>
                    <Input
                      id="first_name"
                      v-model="form.first_name"
                      type="text"
                      required
                      placeholder="e.g., John"
                      :class="{ 'border-destructive': form.errors.first_name }"
                    />
                    <span
                      v-if="form.errors.first_name"
                      class="text-xs text-destructive"
                    >
                      {{ form.errors.first_name }}
                    </span>
                  </div>
                  <div class="space-y-1.5">
                    <Label for="middle_name">Middle Name</Label>
                    <Input
                      id="middle_name"
                      v-model="form.middle_name"
                      type="text"
                      placeholder="(Optional)"
                      :class="{ 'border-destructive': form.errors.middle_name }"
                    />
                    <span
                      v-if="form.errors.middle_name"
                      class="text-xs text-destructive"
                    >
                      {{ form.errors.middle_name }}
                    </span>
                  </div>
                  <div class="space-y-1.5">
                    <Label for="last_name">Last Name</Label>
                    <Input
                      id="last_name"
                      v-model="form.last_name"
                      type="text"
                      required
                      placeholder="e.g., Doe"
                      :class="{ 'border-destructive': form.errors.last_name }"
                    />
                    <span
                      v-if="form.errors.last_name"
                      class="text-xs text-destructive"
                    >
                      {{ form.errors.last_name }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Contact Section -->
              <div class="space-y-6">
                <div class="flex items-center gap-3 border-b pb-3">
                  <Icon
                    icon="lucide:contact"
                    class="h-5 w-5 text-primary"
                  />
                  <h3 class="text-lg font-semibold">Contact Details</h3>
                </div>
                <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                  <div class="space-y-1.5">
                    <Label for="email">Email Address</Label>
                    <Input
                      id="email"
                      v-model="form.email"
                      type="email"
                      required
                      placeholder="your.email@example.com"
                      :class="{ 'border-destructive': form.errors.email }"
                    />
                    <span
                      v-if="form.errors.email"
                      class="text-xs text-destructive"
                    >
                      {{ form.errors.email }}
                    </span>
                  </div>
                  <div class="space-y-1.5">
                    <Label for="contact_number">Contact Number</Label>
                    <Input
                      id="contact_number"
                      v-model="form.contact_number"
                      type="tel"
                      required
                      placeholder="e.g., +63 917 123 4567"
                      :class="{
                        'border-destructive': form.errors.contact_number,
                      }"
                    />
                    <span
                      v-if="form.errors.contact_number"
                      class="text-xs text-destructive"
                    >
                      {{ form.errors.contact_number }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Personal Details & Grade Section -->
              <div class="space-y-6">
                <div class="flex items-center gap-3 border-b pb-3">
                  <Icon
                    icon="lucide:clipboard-user"
                    class="h-5 w-5 text-primary"
                  />
                  <h3 class="text-lg font-semibold">Personal & Academic Snapshot</h3>
                </div>
                <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-3">
                  <div class="space-y-1.5">
                    <Label for="birth_date">Birth Date</Label>
                    <Input
                      id="birth_date"
                      v-model="form.birth_date"
                      type="date"
                      required
                      :class="{ 'border-destructive': form.errors.birth_date }"
                    />
                    <span
                      v-if="form.errors.birth_date"
                      class="text-xs text-destructive"
                    >
                      {{ form.errors.birth_date }}
                    </span>
                  </div>
                  <div class="space-y-1.5">
                    <Label for="gender">Gender</Label>
                    <Select v-model="form.gender" required>
                      <SelectTrigger
                        :class="{ 'border-destructive': form.errors.gender }"
                      >
                        <SelectValue placeholder="Select Gender" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="male">Male</SelectItem>
                        <SelectItem value="female">Female</SelectItem>
                        <SelectItem value="other">Other</SelectItem>
                        <SelectItem value="prefer_not_to_say">Prefer not to say</SelectItem>
                      </SelectContent>
                    </Select>
                    <span
                      v-if="form.errors.gender"
                      class="text-xs text-destructive"
                    >
                      {{ form.errors.gender }}
                    </span>
                  </div>
                  <div class="space-y-1.5">
                    <Label for="current_grade"
                      >Latest Average Grade (%)</Label
                    >
                    <div class="relative">
                      <Input
                        id="current_grade"
                        v-model="form.current_grade"
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        required
                        placeholder="e.g., 85.50"
                        :class="{
                          'border-destructive': form.errors.current_grade,
                        }"
                      />
                      <div
                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted-foreground"
                      >
                        %
                      </div>
                    </div>
                    <p
                      class="text-xs text-muted-foreground flex items-center gap-1 pt-1"
                    >
                      <Icon icon="lucide:info" class="h-3.5 w-3.5" />
                      Minimum 80% required for eligibility.
                    </p>
                    <span
                      v-if="form.errors.current_grade"
                      class="text-xs text-destructive"
                    >
                      {{ form.errors.current_grade }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Address Section -->
              <div class="space-y-6">
                 <div class="flex items-center gap-3 border-b pb-3">
                  <Icon
                    icon="lucide:map-pin"
                    class="h-5 w-5 text-primary"
                  />
                  <h3 class="text-lg font-semibold">Current Address</h3>
                </div>
                <div class="space-y-1.5">
                  <Label for="address">Complete Address</Label>
                  <Textarea
                    id="address"
                    v-model="form.address"
                    required
                    placeholder="e.g., 123 Rizal Street, Brgy. Sample, Sample City, Metro Manila"
                    :class="{ 'border-destructive': form.errors.address }"
                    rows="3"
                  />
                   <p
                      class="text-xs text-muted-foreground flex items-center gap-1 pt-1"
                    >
                      <Icon icon="lucide:info" class="h-3.5 w-3.5" />
                      Please provide your full current residential address.
                    </p>
                  <span
                    v-if="form.errors.address"
                    class="text-xs text-destructive"
                  >
                    {{ form.errors.address }}
                  </span>
                </div>
              </div>

              <!-- Educational Information Section -->
              <div class="space-y-6">
                <div class="flex items-center gap-3 border-b pb-3">
                  <Icon
                    icon="lucide:graduation-cap"
                    class="h-5 w-5 text-primary"
                  />
                  <h3 class="text-lg font-semibold">Educational Background</h3>
                </div>

                <div class="space-y-1.5">
                  <Label for="educational_level"
                    >Current Educational Level</Label
                  >
                  <Select
                    v-model="form.educational_level"
                    @update:modelValue="resetEducationalFields"
                    required
                  >
                    <SelectTrigger
                      :class="{
                        'border-destructive': form.errors.educational_level,
                      }"
                    >
                      <SelectValue placeholder="Select your current level" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="shs">Senior High School</SelectItem>
                      <SelectItem value="college">College</SelectItem>
                      <!-- Add other relevant levels if applicable -->
                    </SelectContent>
                  </Select>
                  <span
                    v-if="form.errors.educational_level"
                    class="text-xs text-destructive"
                  >
                    {{ form.errors.educational_level }}
                  </span>
                </div>

                <!-- Conditional Fields -->
                <div
                  v-if="form.educational_level"
                  class="space-y-6 rounded-md border bg-muted/20 p-4 dark:bg-muted/50"
                >
                  <p class="text-sm font-medium text-primary">
                    Please provide details for your
                    {{ form.educational_level === "shs" ? "Senior High School" : "College" }} level:
                  </p>
                  <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                  <!-- SHS Specific Fields -->
                  <template v-if="form.educational_level === 'shs'">
                      <div class="space-y-1.5">
                      <Label for="strand">Strand/Track</Label>
                      <Input
                        id="strand"
                        v-model="form.strand"
                        type="text"
                        required
                          placeholder="e.g., STEM, ABM, HUMSS"
                        :class="{ 'border-destructive': form.errors.strand }"
                      />
                      <span
                        v-if="form.errors.strand"
                          class="text-xs text-destructive"
                      >
                        {{ form.errors.strand }}
                      </span>
                    </div>
                      <div class="space-y-1.5">
                      <Label for="shs_grade_level">Grade Level</Label>
                        <Select v-model="form.shs_grade_level" required>
                        <SelectTrigger
                          :class="{
                            'border-destructive': form.errors.shs_grade_level,
                          }"
                        >
                          <SelectValue placeholder="Select Grade" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem :value="11">Grade 11</SelectItem>
                          <SelectItem :value="12">Grade 12</SelectItem>
                        </SelectContent>
                      </Select>
                      <span
                        v-if="form.errors.shs_grade_level"
                          class="text-xs text-destructive"
                      >
                        {{ form.errors.shs_grade_level }}
                      </span>
                    </div>
                  </template>

                  <!-- College Specific Fields -->
                  <template v-if="form.educational_level === 'college'">
                      <div class="space-y-1.5">
                      <Label for="course">Course/Program</Label>
                      <Input
                        id="course"
                        v-model="form.course"
                        type="text"
                        required
                          placeholder="e.g., BS Computer Science"
                        :class="{ 'border-destructive': form.errors.course }"
                      />
                      <span
                        v-if="form.errors.course"
                          class="text-xs text-destructive"
                      >
                        {{ form.errors.course }}
                      </span>
                    </div>
                      <div class="space-y-1.5">
                      <Label for="year_level">Year Level</Label>
                        <Select v-model="form.year_level" required>
                        <SelectTrigger
                          :class="{
                            'border-destructive': form.errors.year_level,
                          }"
                        >
                            <SelectValue placeholder="Select Year" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem :value="1">First Year</SelectItem>
                          <SelectItem :value="2">Second Year</SelectItem>
                          <SelectItem :value="3">Third Year</SelectItem>
                          <SelectItem :value="4">Fourth Year</SelectItem>
                          <SelectItem :value="5">Fifth Year</SelectItem>
                            <SelectItem :value="6">More than 5 Years / Graduate</SelectItem>
                        </SelectContent>
                      </Select>
                      <span
                        v-if="form.errors.year_level"
                          class="text-xs text-destructive"
                      >
                        {{ form.errors.year_level }}
                      </span>
                    </div>
                     <!-- New Semester System Field -->
                    <div class="space-y-1.5 sm:col-span-2">
                      <Label for="semester_system">Semester System</Label>
                      <Select v-model="form.semester_system" required>
                        <SelectTrigger
                          :class="{
                            'border-destructive': form.errors.semester_system,
                          }"
                        >
                          <SelectValue placeholder="Select your school's semester system" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="semestral">Semestral (2 Semesters per year)</SelectItem>
                          <SelectItem value="trimesteral">Trimesteral (3 Semesters per year)</SelectItem>
                        </SelectContent>
                      </Select>
                      <span
                        v-if="form.errors.semester_system"
                        class="text-xs text-destructive"
                      >
                        {{ form.errors.semester_system }}
                      </span>
                    </div>
                  </template>
                </div>
                  <!-- School Selection (Common within conditional block) -->
                   <div class="space-y-1.5">
                    <Label for="school_id">School/University Attending</Label>
                    <Select v-model="form.school_id" required>
                    <SelectTrigger
                      :class="{ 'border-destructive': form.errors.school_id }"
                    >
                        <SelectValue :placeholder="filteredSchools.length > 0 ? 'Select your school' : 'No schools available for this level'" />
                    </SelectTrigger>
                    <SelectContent>
                        <template v-if="filteredSchools.length > 0">
                      <SelectItem
                            v-for="school in filteredSchools"
                        :key="school.id"
                        :value="school.id"
                      >
                        {{ school.name }}
                          </SelectItem>
                        </template>
                        <SelectItem v-else value="" disabled>
                           {{ form.educational_level ? 'No matching schools found' : 'Select educational level first' }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                     <p
                      class="text-xs text-muted-foreground flex items-center gap-1 pt-1"
                    >
                      <Icon icon="lucide:info" class="h-3.5 w-3.5" />
                       Schools are filtered based on your selected educational level.
                    </p>
                  <span
                    v-if="form.errors.school_id"
                      class="text-xs text-destructive"
                  >
                    {{ form.errors.school_id }}
                  </span>
                  </div>
                </div>


                <!-- Enrollment Intent Section (Common) -->
                <div v-if="form.educational_level" class="space-y-1.5 pt-2">
                  <Label for="enrollment_intent"
                    >Educational & Career Goals</Label
                  >
                  <Textarea
                    id="enrollment_intent"
                    v-model="form.enrollment_intent"
                    required
                    :class="{
                      'border-destructive': form.errors.enrollment_intent,
                    }"
                    rows="4"
                    placeholder="Briefly describe your academic aspirations, why you chose your current path, and your future career goals (max 2000 characters)."
                  />
                  <p
                    class="text-xs text-muted-foreground flex items-center gap-1 pt-1"
                  >
                    <Icon icon="lucide:alert-circle" class="h-3.5 w-3.5" />
                    This helps us understand your motivation and plans.
                  </p>
                  <span
                    v-if="form.errors.enrollment_intent"
                    class="text-xs text-destructive"
                  >
                    {{ form.errors.enrollment_intent }}
                  </span>
                </div>
              </div>

              <div class="flex justify-end pt-6 border-t">
                <Button
                  type="submit"
                  size="lg"
                  :disabled="form.processing || !form.educational_level"
                  :class="{
                    'opacity-50 cursor-not-allowed':
                      form.processing || !form.educational_level,
                  }"
                >
                  <Icon
                    v-if="form.processing"
                    icon="lucide:loader-2"
                    class="mr-2 h-5 w-5 animate-spin"
                  />
                  <Icon
                    v-else
                    icon="lucide:check-circle-2"
                    class="mr-2 h-5 w-5"
                  />
                  {{
                    form.processing ? "Checking Eligibility..." : "Check Eligibility & Proceed"
                  }}
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
        <div class="text-center text-sm text-muted-foreground">
            Already have an account or pre-qualified?
            <a :href="route('login')" class="font-medium text-primary hover:underline">Login here</a>.
            <span v-if="canRegister">
                If you are eligible, you will be redirected to create an account.
            </span>
        </div>
      </div>
    </div>
    <Toaster richColors position="top-right" />
  </web-layout>
</template>

<script setup>
import { ref, computed, onMounted, h } from "vue";
import { useForm, usePage, Link } from "@inertiajs/vue3";
import WebLayout from "@/Layouts/WebLayout.vue";
import Card from "@/Components/shadcn/ui/card/Card.vue";
import CardHeader from "@/Components/shadcn/ui/card/CardHeader.vue";
import CardTitle from "@/Components/shadcn/ui/card/CardTitle.vue";
import CardDescription from "@/Components/shadcn/ui/card/CardDescription.vue";
import CardContent from "@/Components/shadcn/ui/card/CardContent.vue";
import Input from "@/Components/shadcn/ui/input/Input.vue";
import Label from "@/Components/shadcn/ui/label/Label.vue";
import Button from "@/Components/shadcn/ui/button/Button.vue";
import Select from "@/Components/shadcn/ui/select/Select.vue";
import SelectTrigger from "@/Components/shadcn/ui/select/SelectTrigger.vue";
import SelectValue from "@/Components/shadcn/ui/select/SelectValue.vue";
import SelectContent from "@/Components/shadcn/ui/select/SelectContent.vue";
import SelectItem from "@/Components/shadcn/ui/select/SelectItem.vue";
import Textarea from "@/Components/shadcn/ui/textarea/Textarea.vue";
import { Toaster, toast } from "vue-sonner";
import { Icon } from "@iconify/vue";

// Props definition (ensure schools prop includes 'type')
const props = defineProps({
  schools: Array, // Expected structure: [{ id: 1, name: 'School A', type: 'shs' }, ...]
  canLogin: Boolean,
  canRegister: Boolean,
  // flash: Object, // flash messages are usually part of page.props
});

const page = usePage();
// processing is part of form object now, no need for separate ref: form.processing

const form = useForm({
  first_name: "",
  middle_name: "",
  last_name: "",
  email: "",
  contact_number: "",
  address: "",
  birth_date: "",
  gender: null,
  current_grade: "",
  educational_level: null, // 'shs' or 'college'
  school_id: null,
  enrollment_intent: "",
  strand: "",
  shs_grade_level: null,
  course: "",
  year_level: null,
  semester_system: null, // Added for college semester system
});

// Computed property to filter schools based on selected educational level
const filteredSchools = computed(() => {
  if (!form.educational_level || !props.schools) {
    return [];
  }
  // Assuming school.type is 'shs' or 'college'
  // Adapt this if your school types are different (e.g. array of types, or more specific types)
  return props.schools.filter(
    (school) => school.type && school.type.toLowerCase() === form.educational_level.toLowerCase()
  );
});

// Function to reset conditional fields when educational level changes
const resetEducationalFields = () => {
  form.strand = "";
  form.shs_grade_level = null;
  form.course = "";
  form.year_level = null;
  form.semester_system = null; // Reset semester system
  form.school_id = null; // Reset school selection
  form.enrollment_intent = "";

  // Clear related errors
  form.clearErrors(
    "strand",
    "shs_grade_level",
    "course",
    "year_level",
    "semester_system", // Clear semester system error
    "school_id",
    "enrollment_intent",
  );
};

const submit = () => {
  // form.processing is automatically handled by useForm
  form.post(route("pre-qualification.store"), {
    preserveScroll: true,
    onSuccess: (page) => {
      // Controller handles redirection and success flash message.
      // Inertia's default flash message handling should pick up success message.
      // If 'page.props.flash.success' exists, it's often handled by a layout component.
      // No need for explicit toast here if backend redirect + session flash works.
       if (page.props.flash && page.props.flash.success) {
         toast.success(page.props.flash.success, {
            duration: 5000,
            icon: h(Icon, { icon: "lucide:check-check", class: "h-5 w-5" }),
        });
       }
    },
    onError: (errors) => {
      console.error("Form submission errors:", errors);
      if (page.props.flash && page.props.flash.error) {
        // This handles backend-set errors (like eligibility failure)
        toast.error(page.props.flash.error, {
          duration: 6000,
          icon: h(Icon, { icon: "lucide:alert-octagon", class: "h-5 w-5" }),
        });
      } else {
        // This handles validation errors from Inertia
        const firstErrorKey = Object.keys(errors)[0];
        if (firstErrorKey) {
          toast.error(errors[firstErrorKey], {
            description: `Please check the ${firstErrorKey.replace(
              /_/g,
              " ",
            )} field and other highlighted fields.`,
            duration: 6000,
            icon: h(Icon, { icon: "lucide:alert-triangle", class: "h-5 w-5" }),
          });
        } else {
          toast.error("An unexpected error occurred. Please review the form and try again.", {
            duration: 6000,
            icon: h(Icon, { icon: "lucide:server-crash", class: "h-5 w-5" }),
          });
        }
      }
    },
    // onFinish: () => {
      // form.processing is automatically reset by useForm
    // },
  });
};

// Handle initial flash messages (e.g., if redirected with an error before form submission)
onMounted(() => {
  // Check for flash messages from the backend on initial load
  // Note: Inertia typically provides flash messages in `page.props.flash`
  const flashSuccess = page.props.flash?.success;
  const flashError = page.props.flash?.error;

  if (flashSuccess) {
    toast.success(flashSuccess, {
      duration: 5000,
        icon: h(Icon, { icon: "lucide:party-popper", class: "h-5 w-5" }),
    });
    // Clear flash after showing, or rely on Inertia middleware to handle it
    // if (page.props.jetstream) page.props.jetstream.flash = null;
    // else page.props.flash = {};
  }
  if (flashError) {
    toast.error(flashError, {
        duration: 6000,
        icon: h(Icon, { icon: "lucide:alert-octagon", class: "h-5 w-5" }),
    });
    // Clear flash
    // if (page.props.jetstream) page.props.jetstream.flash = null;
    // else page.props.flash = {};
  }
});
</script>

<style scoped>
/* Add any additional component-specific styles here */
/* For example, fine-tuning spacing or specific element appearances */
.container {
  max-width: 800px; /* Or your preferred max width */
}
</style>
