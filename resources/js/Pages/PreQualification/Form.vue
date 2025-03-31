<template>
  <web-layout>
    <div class="container py-12">
      <div class="mx-auto max-w-3xl space-y-8">
        <div class="space-y-4 text-center">
          <h1 class="text-4xl font-bold tracking-tight">
            Scholarship Pre-Qualification
          </h1>
          <p class="text-lg text-muted-foreground">
            Take the first step towards your educational journey with our
            scholarship program.
          </p>
        </div>

        <Card>
          <CardHeader>
            <div class="flex items-center gap-4">
              <div
                class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center"
              >
                <Icon icon="lucide:user" class="h-5 w-5 text-primary" />
              </div>
              <div>
                <CardTitle>Personal Information</CardTitle>
                <CardDescription>
                  Please provide your details accurately for verification
                  purposes.
                </CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-8">
              <!-- Name Section -->
              <div class="space-y-4">
                <div class="flex items-center gap-2">
                  <Icon
                    icon="lucide:user"
                    class="h-4 w-4 text-muted-foreground"
                  />
                  <h3 class="font-medium">Your Name</h3>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                  <div class="space-y-2">
                    <Label for="first_name">First Name</Label>
                    <Input
                      id="first_name"
                      v-model="form.first_name"
                      type="text"
                      required
                      placeholder="John"
                      :class="{ 'border-destructive': form.errors.first_name }"
                    />
                    <span
                      v-if="form.errors.first_name"
                      class="text-sm text-destructive"
                    >
                      {{ form.errors.first_name }}
                    </span>
                  </div>
                  <div class="space-y-2">
                    <Label for="middle_name">Middle Name</Label>
                    <Input
                      id="middle_name"
                      v-model="form.middle_name"
                      type="text"
                      placeholder="(Optional)"
                    />
                  </div>
                  <div class="space-y-2">
                    <Label for="last_name">Last Name</Label>
                    <Input
                      id="last_name"
                      v-model="form.last_name"
                      type="text"
                      required
                      placeholder="Doe"
                      :class="{ 'border-destructive': form.errors.last_name }"
                    />
                    <span
                      v-if="form.errors.last_name"
                      class="text-sm text-destructive"
                    >
                      {{ form.errors.last_name }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Contact Section -->
              <div class="space-y-4">
                <div class="flex items-center gap-2">
                  <Icon
                    icon="lucide:contact"
                    class="h-4 w-4 text-muted-foreground"
                  />
                  <h3 class="font-medium">Contact Information</h3>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="email">Email Address</Label>
                    <Input
                      id="email"
                      v-model="form.email"
                      type="email"
                      required
                      placeholder="john.doe@example.com"
                      :class="{ 'border-destructive': form.errors.email }"
                    />
                    <span
                      v-if="form.errors.email"
                      class="text-sm text-destructive"
                    >
                      {{ form.errors.email }}
                    </span>
                  </div>
                  <div class="space-y-2">
                    <Label for="contact_number">Contact Number</Label>
                    <Input
                      id="contact_number"
                      v-model="form.contact_number"
                      type="tel"
                      required
                      placeholder="+63 XXX XXX XXXX"
                      :class="{
                        'border-destructive': form.errors.contact_number,
                      }"
                    />
                    <span
                      v-if="form.errors.contact_number"
                      class="text-sm text-destructive"
                    >
                      {{ form.errors.contact_number }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Personal Details Section -->
              <div class="space-y-4">
                <div class="flex items-center gap-2">
                  <Icon
                    icon="lucide:id-card"
                    class="h-4 w-4 text-muted-foreground"
                  />
                  <h3 class="font-medium">Personal Details</h3>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                  <div class="space-y-2">
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
                      class="text-sm text-destructive"
                    >
                      {{ form.errors.birth_date }}
                    </span>
                  </div>
                  <div class="space-y-2">
                    <Label for="gender">Gender</Label>
                    <Select v-model="form.gender">
                      <SelectTrigger
                        :class="{ 'border-destructive': form.errors.gender }"
                      >
                        <SelectValue placeholder="Select Gender" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="male">Male</SelectItem>
                        <SelectItem value="female">Female</SelectItem>
                        <SelectItem value="other">Other</SelectItem>
                      </SelectContent>
                    </Select>
                    <span
                      v-if="form.errors.gender"
                      class="text-sm text-destructive"
                    >
                      {{ form.errors.gender }}
                    </span>
                  </div>
                  <div class="space-y-2">
                    <Label for="current_grade">Current Grade (%)</Label>
                    <div class="relative">
                      <Input
                        id="current_grade"
                        v-model="form.current_grade"
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        required
                        placeholder="85.00"
                        :class="{
                          'border-destructive': form.errors.current_grade,
                        }"
                      />
                      <div
                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                      >
                        <span class="text-sm text-muted-foreground">%</span>
                      </div>
                    </div>
                    <p
                      class="text-sm text-muted-foreground flex items-center gap-1"
                    >
                      <Icon icon="lucide:info" class="h-3 w-3" />
                      Minimum requirement: 80%
                    </p>
                    <span
                      v-if="form.errors.current_grade"
                      class="text-sm text-destructive"
                    >
                      {{ form.errors.current_grade }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Address Section -->
              <div class="space-y-4">
                <div class="flex items-center gap-2">
                  <Icon
                    icon="lucide:map-pin"
                    class="h-4 w-4 text-muted-foreground"
                  />
                  <h3 class="font-medium">Address Information</h3>
                </div>
                <div class="space-y-2">
                  <Label for="address">Complete Address</Label>
                  <Textarea
                    id="address"
                    v-model="form.address"
                    required
                    placeholder="Enter your complete address..."
                    :class="{ 'border-destructive': form.errors.address }"
                    rows="3"
                  />
                  <span
                    v-if="form.errors.address"
                    class="text-sm text-destructive"
                  >
                    {{ form.errors.address }}
                  </span>
                </div>
              </div>

              <!-- Educational Information Section -->
              <div class="space-y-4">
                <div class="flex items-center gap-2">
                  <Icon
                    icon="lucide:graduation-cap"
                    class="h-4 w-4 text-muted-foreground"
                  />
                  <h3 class="font-medium">Educational Information</h3>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="course">Course/Program</Label>
                    <Input
                      id="course"
                      v-model="form.course"
                      type="text"
                      required
                      placeholder="e.g., Computer Science"
                      :class="{ 'border-destructive': form.errors.course }"
                    />
                    <span
                      v-if="form.errors.course"
                      class="text-sm text-destructive"
                    >
                      {{ form.errors.course }}
                    </span>
                  </div>
                  <div class="space-y-2">
                    <Label for="year_level">Year Level</Label>
                    <Select v-model="form.year_level">
                      <SelectTrigger
                        :class="{
                          'border-destructive': form.errors.year_level,
                        }"
                      >
                        <SelectValue placeholder="Select Year Level" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem :value="1">First Year</SelectItem>
                        <SelectItem :value="2">Second Year</SelectItem>
                        <SelectItem :value="3">Third Year</SelectItem>
                        <SelectItem :value="4">Fourth Year</SelectItem>
                        <SelectItem :value="5">Fifth Year</SelectItem>
                        <SelectItem :value="6">Graduate Studies</SelectItem>
                      </SelectContent>
                    </Select>
                    <span
                      v-if="form.errors.year_level"
                      class="text-sm text-destructive"
                    >
                      {{ form.errors.year_level }}
                    </span>
                  </div>
                </div>

                <div class="space-y-2">
                  <Label for="school">School/University</Label>
                  <Select v-model="form.school_id">
                    <SelectTrigger
                      :class="{ 'border-destructive': form.errors.school_id }"
                    >
                      <SelectValue placeholder="Select School" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem
                        v-for="school in schools"
                        :key="school.id"
                        :value="school.id"
                      >
                        {{ school.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                  <span
                    v-if="form.errors.school_id"
                    class="text-sm text-destructive"
                  >
                    {{ form.errors.school_id }}
                  </span>
                </div>

                <!-- Enrollment Intent Section -->
                <div class="space-y-2">
                  <Label for="enrollment_intent">Enrollment Intent</Label>
                  <Textarea
                    id="enrollment_intent"
                    v-model="form.enrollment_intent"
                    required
                    :class="{
                      'border-destructive': form.errors.enrollment_intent,
                    }"
                    rows="3"
                    placeholder="Please describe your educational goals and plans..."
                  />
                  <p
                    class="text-sm text-muted-foreground flex items-center gap-1"
                  >
                    <Icon icon="lucide:info" class="h-3 w-3" />
                    Tell us about your academic aspirations and career goals
                  </p>
                  <span
                    v-if="form.errors.enrollment_intent"
                    class="text-sm text-destructive"
                  >
                    {{ form.errors.enrollment_intent }}
                  </span>
                </div>
              </div>

              <div class="flex justify-end space-x-2">
                <Button
                  type="submit"
                  :disabled="processing"
                  :class="{ 'opacity-50 cursor-not-allowed': processing }"
                >
                  <Icon
                    v-if="processing"
                    icon="lucide:loader-2"
                    class="mr-2 h-4 w-4 animate-spin"
                  />
                  <Icon
                    v-else
                    icon="lucide:check-circle"
                    class="mr-2 h-4 w-4"
                  />
                  {{
                    processing ? "Checking Eligibility..." : "Check Eligibility"
                  }}
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </web-layout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
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
import Sonner from "@/Components/shadcn/ui/sonner/Sonner.vue";
import { Icon } from "@iconify/vue";
import { toast } from "vue-sonner";

const page = usePage();
const processing = ref(false);

// This would normally come from your API, but for demonstration we'll use a static list
const schools = ref([
  { id: 1, name: "University of the Philippines" },
  { id: 2, name: "Ateneo de Manila University" },
  { id: 3, name: "De La Salle University" },
  { id: 4, name: "University of Santo Tomas" },
  { id: 5, name: "Polytechnic University of the Philippines" },
]);

const form = useForm({
  first_name: "",
  middle_name: "",
  last_name: "",
  email: "",
  contact_number: "",
  address: "",
  birth_date: "",
  gender: "",
  current_grade: "",
  school_id: "",
  course: "",
  year_level: "",
  enrollment_intent: "",
  // Pre-set fields based on Scholar model
  type: "scholar", // This is set automatically
  status: "pending", // This is set automatically
});

const submit = async () => {
  processing.value = true;
  try {
    form.post(route("pre-qualification.store"), {
      onSuccess: () => {
        if (page.props.flash.success) {
          toast.success(page.props.flash.success, {
            duration: 5000,
            icon: h(Icon, { icon: "lucide:check-circle", class: "h-5 w-5" }),
          });
        }
      },
      onError: (errors) => {
        if (page.props.flash.error) {
          toast.error(page.props.flash.error, {
            duration: 5000,
            icon: h(Icon, { icon: "lucide:alert-circle", class: "h-5 w-5" }),
          });
        } else {
          // Show form validation errors
          const firstError = Object.values(errors)[0];
          if (firstError) {
            toast.error(firstError, {
              duration: 5000,
              icon: h(Icon, { icon: "lucide:alert-circle", class: "h-5 w-5" }),
            });
          }
        }
      },
    });
  } finally {
    processing.value = false;
  }
};

// Check for flash messages on component mount
onMounted(() => {
  if (page.props.flash.error) {
    toast.error(page.props.flash.error, {
      duration: 5000,
      icon: h(Icon, { icon: "lucide:alert-circle", class: "h-5 w-5" }),
    });
  }

  if (page.props.flash.success) {
    toast.success(page.props.flash.success, {
      duration: 5000,
      icon: h(Icon, { icon: "lucide:check-circle", class: "h-5 w-5" }),
    });
  }
});
</script>
