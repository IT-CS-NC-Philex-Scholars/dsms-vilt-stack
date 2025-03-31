<script setup>

import AppLayout from "@/Layouts/AppLayout.vue";
// Import Shadcn UI Vue Components

import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter,
} from "@/Components/shadcn/ui/card";

import { Button } from "@/Components/shadcn/ui/button";
import { Badge } from "@/Components/shadcn/ui/badge";
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from "@/Components/shadcn/ui/avatar";
import {
  Alert,
  AlertDescription,
  AlertTitle,
} from "@/Components/shadcn/ui/alert";
import { Progress } from "@/Components/shadcn/ui/progress"; // Import Progress
import { Separator } from "@/Components/shadcn/ui/separator"; // Import Separator

// If using @iconify/vue:
import { Icon } from "@iconify/vue";
// Or if using lucide-vue-next:
// import { Check, CheckCircle2, AlertCircle, Info, Hourglass, FileText, User, Bell, LifeBuoy, Mail, Settings2, FileSearch2, PencilLine, XCircle, FilePlus2, AlertTriangle, Target, ListChecks, Banknote, CalendarCheck } from 'lucide-vue-next';

import { usePage, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const user = computed(() => page.props.auth.user);
const appName = computed(() => page.props.appName || "PhilexScholar");

// --- DUMMY DATA (Replace with actual props/data) ---
const application = computed(() => {
  // Statuses: 'Not Started', 'In Progress', 'Submitted', 'Under Review', 'Needs Information', 'Approved', 'Rejected'
  const baseData = {
    id: 1,
    scholarshipName: "Future Leaders Scholarship 2024",
    status: "Approved", // <-- CHANGE THIS TO TEST ('Needs Information', 'In Progress', 'Submitted', 'Approved', null)
    lastUpdate: "2024-07-28",
    nextDeadline: null, // Example: '2024-08-15',
    submissionDate: "2024-07-15",
    missingDocuments: ["Proof of Enrollment", "Reference Letter"],
    notificationsCount: 1,
    awardAmount: 5000,
    awardPeriod: "Academic Year 2024-2025",
    profileCompleteness: 90, // Percentage
    // Example Tasks based on status
    tasks: [],
    // Example Payments based on status
    payments: [],
    // Example Steps (total and current step number)
    totalSteps: 5,
    currentStep: 5, // Step number based on status
  };

  // Adjust tasks, payments, and currentStep based on status
  if (baseData.status === "Needs Information") {
    baseData.tasks = baseData.missingDocuments.map((doc) => ({
      id: doc,
      description: `Upload: ${doc}`,
      urgent: true,
    }));
    baseData.currentStep = 3;
  } else if (baseData.status === "In Progress") {
    baseData.tasks = [
      {
        id: "complete_profile",
        description: "Complete profile information",
        urgent: false,
      },
      {
        id: "upload_essay",
        description: "Upload personal essay",
        urgent: true,
      },
    ];
    baseData.currentStep = 2;
  } else if (baseData.status === "Approved") {
    baseData.tasks = [
      {
        id: "accept_offer",
        description: "Accept Scholarship Offer",
        urgent: true,
      },
    ];
    baseData.currentStep = 5;
    baseData.payments = [
      { id: "p1", date: "2024-09-01", amount: 2500, status: "Scheduled" },
      { id: "p2", date: "2025-01-15", amount: 2500, status: "Scheduled" },
    ];
  } else if (
    baseData.status === "Submitted" ||
    baseData.status === "Under Review"
  ) {
    baseData.currentStep = 4;
  } else if (!baseData.status || baseData.status === "Not Started") {
    baseData.currentStep = 1;
    return null; // No application yet
  }

  return baseData;
  // return null; // Use this to test the 'Not Started' state
});
// --- END DUMMY DATA ---

const userName = computed(() => user.value?.name || "Scholar");
const userInitials = computed(() => {
  return (
    userName.value
      ?.match(/\b(\w)/g)
      ?.join("")
      .toUpperCase()
      .slice(0, 2) || "S"
  );
});

// Status Badge Variant Mapping (keep as before)
const getStatusVariant = (status) => {
  /* ... keep previous logic ... */
  switch (status?.toLowerCase()) {
    case "approved":
      return "success";
    case "submitted":
    case "under review":
      return "info";
    case "needs information":
      return "warning";
    case "rejected":
      return "destructive";
    case "in progress":
      return "secondary";
    default:
      return "outline";
  }
};

// Icon Mapping (keep as before)
const getStatusIcon = (status) => {
  /* ... keep previous logic ... */
  switch (status?.toLowerCase()) {
    case "approved":
      return "lucide:check-circle-2";
    case "submitted":
    case "under review":
      return "lucide:hourglass";
    case "needs information":
      return "lucide:alert-circle";
    case "rejected":
      return "lucide:x-circle";
    case "in progress":
      return "lucide:pencil-line";
    default:
      return "lucide:file-plus-2";
  }
};

// Primary Action Button Logic (keep as before, adjust routes)
const primaryAction = computed(() => {
  /* ... keep previous logic ... */
  if (!application.value)
    return { text: "Find Scholarships", href: "#", variant: "default" }; // route('scholarships.index')
  switch (application.value.status?.toLowerCase()) {
    case "not started":
    case "in progress":
      return { text: "Continue Application", href: "#", variant: "default" }; // route('applications.edit', application.value.id)
    case "needs information":
      return { text: "Upload Documents", href: "#", variant: "default" }; // route('applications.documents', application.value.id)
    case "approved":
      return { text: "Accept Offer", href: "#", variant: "default" }; // route('applications.accept', application.value.id) - Changed logic
    default:
      return null;
  }
});

// Secondary Action Button Logic
const secondaryAction = computed(() => {
  if (!application.value) return null;
  // Always show view details, except maybe if not started
  if (application.value.status !== "Not Started") {
    return { text: "View Application Details", href: "#", variant: "outline" }; // route('applications.show', application.value.id)
  }
  return null;
});

// Format Currency (keep as before)
const formatCurrency = (value) => {
  /* ... keep previous logic ... */
  if (value == null) return "";
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(value);
};

// Format Date (utility)
const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};
</script>

<template>
  <AppLayout :title="`${appName} Dashboard`">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-foreground">
        Scholar Dashboard
      </h2>
    </template>

    <div class="py-8 sm:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 lg:space-y-8">
        <!-- Welcome Message -->
        <div class="px-4 sm:px-0">
          <h1 class="text-2xl font-semibold text-foreground">
            Welcome back, {{ userName }}!
          </h1>
          <p class="mt-1 text-sm text-muted-foreground">
            Manage your scholarships, track progress, and complete necessary
            tasks.
          </p>
        </div>

        <!-- Main Grid -->
        <div
          class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 px-4 sm:px-0"
        >
          <!-- === Left Column (Application Focus) === -->
          <div class="lg:col-span-2 space-y-6 lg:space-y-8">
            <!-- Application Overview Card -->
            <Card
              class="shadow-sm transition-shadow hover:shadow-md overflow-hidden"
            >
              <CardHeader class="bg-muted/30 dark:bg-card">
                <div
                  class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2"
                >
                  <CardTitle class="text-lg font-semibold">
                    {{
                      application
                        ? application.scholarshipName
                        : "Application Overview"
                    }}
                  </CardTitle>
                  <Badge
                    v-if="application"
                    :variant="getStatusVariant(application.status)"
                    class="capitalize self-start sm:self-center"
                  >
                    <Icon
                      :icon="getStatusIcon(application.status)"
                      class="h-3.5 w-3.5 mr-1.5 -ml-0.5"
                    />
                    {{ application.status }}
                  </Badge>
                  <Badge
                    v-else
                    variant="outline"
                    class="self-start sm:self-center"
                  >
                    <Icon
                      :icon="getStatusIcon(null)"
                      class="h-3.5 w-3.5 mr-1.5 -ml-0.5"
                    />
                    Not Started
                  </Badge>
                </div>
                <CardDescription
                  v-if="application?.lastUpdate"
                  class="mt-1 text-xs"
                >
                  Last updated: {{ formatDate(application.lastUpdate) }}
                </CardDescription>
              </CardHeader>
              <CardContent class="pt-4 pb-5 space-y-4">
                <template v-if="application">
                  <!-- Progress Indicator -->
                  <div>
                    <div
                      class="flex justify-between mb-1 text-xs font-medium text-muted-foreground"
                    >
                      <span>Application Progress</span>
                      <span
                        >Step {{ application.currentStep }} of
                        {{ application.totalSteps }}</span
                      >
                    </div>
                    <Progress
                      :model-value="
                        (application.currentStep / application.totalSteps) * 100
                      "
                      class="h-2"
                    />
                  </div>

                  <!-- Key Details -->
                  <dl
                    class="text-sm grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 border-t border-dashed pt-4"
                  >
                    <div class="flex justify-between sm:block">
                      <dt class="text-muted-foreground font-normal">
                        Submitted:
                      </dt>
                      <dd class="font-medium text-foreground">
                        {{ formatDate(application.submissionDate) }}
                      </dd>
                    </div>
                    <div class="flex justify-between sm:block">
                      <dt class="text-muted-foreground font-normal">
                        Next Deadline:
                      </dt>
                      <dd
                        class="font-semibold"
                        :class="
                          application.nextDeadline
                            ? 'text-orange-600 dark:text-orange-400'
                            : 'text-muted-foreground'
                        "
                      >
                        {{
                          application.nextDeadline
                            ? formatDate(application.nextDeadline)
                            : "None"
                        }}
                      </dd>
                    </div>
                    <div
                      class="flex justify-between sm:block"
                      v-if="application.status === 'Approved'"
                    >
                      <dt class="text-muted-foreground font-normal">
                        Award Amount:
                      </dt>
                      <dd
                        class="font-semibold text-green-600 dark:text-green-400"
                      >
                        {{ formatCurrency(application.awardAmount) }}
                      </dd>
                    </div>
                    <div
                      class="flex justify-between sm:block"
                      v-if="application.status === 'Approved'"
                    >
                      <dt class="text-muted-foreground font-normal">
                        Award Period:
                      </dt>
                      <dd class="font-medium text-foreground">
                        {{ application.awardPeriod }}
                      </dd>
                    </div>
                  </dl>
                </template>
                <template v-else>
                  <!-- No Application State -->
                  <div class="text-center py-6">
                    <Icon
                      icon="lucide:file-search-2"
                      class="h-10 w-10 text-muted-foreground mx-auto mb-3"
                    />
                    <p class="font-medium text-foreground">
                      No Active Application
                    </p>
                    <p class="text-sm text-muted-foreground mt-1">
                      Ready to find your next opportunity?
                    </p>
                  </div>
                </template>
              </CardContent>
              <CardFooter
                v-if="primaryAction || secondaryAction"
                class="border-t pt-4 flex items-center gap-3 bg-muted/30 dark:bg-card"
              >
                <Button
                  v-if="primaryAction"
                  :as="Link"
                  :href="primaryAction.href"
                  :variant="primaryAction.variant"
                  size="sm"
                >
                  <Icon
                    :icon="getStatusIcon(application?.status)"
                    class="mr-1.5 h-4 w-4"
                    v-if="application?.status !== 'Needs Information'"
                  />
                  <Icon
                    icon="lucide:upload-cloud"
                    class="mr-1.5 h-4 w-4"
                    v-if="application?.status === 'Needs Information'"
                  />
                  {{ primaryAction.text }}
                </Button>
                <Button
                  v-if="secondaryAction"
                  :as="Link"
                  :href="secondaryAction.href"
                  :variant="secondaryAction.variant"
                  size="sm"
                >
                  {{ secondaryAction.text }}
                </Button>
              </CardFooter>
            </Card>

            <!-- Pending Tasks / Action Required Card -->
            <Card
              v-if="application?.tasks?.length > 0"
              class="shadow-sm border-l-4"
              :class="{
                'border-amber-500 dark:border-amber-400':
                  application.tasks.some((t) => t.urgent),
                'border-blue-500 dark:border-blue-400': !application.tasks.some(
                  (t) => t.urgent,
                ),
              }"
            >
              <CardHeader class="pb-2 pt-4 px-4">
                <CardTitle
                  class="text-base font-semibold flex items-center gap-2"
                >
                  <Icon
                    icon="lucide:list-checks"
                    class="h-5 w-5"
                    :class="{
                      'text-amber-500 dark:text-amber-400':
                        application.tasks.some((t) => t.urgent),
                      'text-blue-500 dark:text-blue-400':
                        !application.tasks.some((t) => t.urgent),
                    }"
                  />
                  Pending Tasks ({{ application.tasks.length }})
                </CardTitle>
              </CardHeader>
              <CardContent class="px-4 pb-4">
                <ul class="space-y-2 text-sm">
                  <li
                    v-for="task in application.tasks"
                    :key="task.id"
                    class="flex items-start gap-2"
                  >
                    <Icon
                      icon="lucide:alert-circle"
                      class="h-4 w-4 mt-0.5 shrink-0 text-amber-500"
                      v-if="task.urgent"
                    />
                    <Icon
                      icon="lucide:circle-dashed"
                      class="h-4 w-4 mt-0.5 shrink-0 text-muted-foreground"
                      v-else
                    />
                    <span
                      :class="
                        task.urgent
                          ? 'font-medium text-foreground'
                          : 'text-muted-foreground'
                      "
                      >{{ task.description }}</span
                    >
                    <!-- Optional: Add action button per task -->
                    <!-- <Button size="xs" variant="outline" class="ml-auto">Go</Button> -->
                  </li>
                </ul>
              </CardContent>
            </Card>

            <!-- Financial Summary Card (Conditional) -->
            <Card
              v-if="
                application?.status === 'Approved' &&
                application?.payments?.length > 0
              "
              class="shadow-sm"
            >
              <CardHeader>
                <CardTitle
                  class="text-base font-semibold flex items-center gap-2"
                >
                  <Icon
                    icon="lucide:banknote"
                    class="h-5 w-5 text-green-600 dark:text-green-400"
                  />
                  Financial Summary
                </CardTitle>
                <CardDescription class="text-xs"
                  >Upcoming and past disbursements.</CardDescription
                >
              </CardHeader>
              <CardContent>
                <ul class="space-y-3">
                  <li
                    v-for="payment in application.payments"
                    :key="payment.id"
                    class="flex items-center justify-between text-sm border-b border-dashed pb-2 last:border-0 last:pb-0"
                  >
                    <div class="flex items-center gap-2">
                      <Icon
                        icon="lucide:calendar-check"
                        class="h-4 w-4 text-muted-foreground"
                      />
                      <span class="font-medium text-foreground">{{
                        formatDate(payment.date)
                      }}</span>
                    </div>
                    <div class="text-right">
                      <span class="font-semibold text-foreground">{{
                        formatCurrency(payment.amount)
                      }}</span>
                      <Badge
                        :variant="
                          payment.status === 'Paid' ? 'success' : 'secondary'
                        "
                        class="ml-2 text-xs capitalize"
                        >{{ payment.status }}</Badge
                      >
                    </div>
                  </li>
                </ul>
              </CardContent>
              <CardFooter v-if="false">
                <!-- Placeholder for potential footer actions -->
                <Button variant="link" size="sm">View Full History</Button>
              </CardFooter>
            </Card>
          </div>
          <!-- === END Left Column === -->

          <!-- === Right Sidebar Column === -->
          <div class="space-y-6 lg:space-y-8">
            <!-- Profile Card -->
            <Card class="shadow-sm overflow-hidden">
              <CardHeader
                class="flex flex-row items-center gap-4 space-y-0 p-4 bg-gradient-to-b from-muted/40 to-transparent dark:from-card"
              >
                <Avatar class="h-14 w-14 border-2 border-background">
                  <AvatarImage :src="user?.profile_photo_url" :alt="userName" />
                  <AvatarFallback class="text-lg">{{
                    userInitials
                  }}</AvatarFallback>
                </Avatar>
                <div class="flex-1 min-w-0">
                  <CardTitle class="text-base font-semibold truncate">{{
                    userName
                  }}</CardTitle>
                  <CardDescription class="text-xs truncate">{{
                    user?.email
                  }}</CardDescription>
                  <Button
                    :as="Link"
                    href="#"
                    variant="outline"
                    size="xs"
                    class="mt-1.5 h-6 px-2"
                  >
                    <!-- route('profile.show') -->
                    <Icon icon="lucide:settings-2" class="h-3 w-3 mr-1" /> Edit
                    Profile
                  </Button>
                </div>
              </CardHeader>
              <CardContent class="p-4 text-xs">
                <div
                  class="flex justify-between items-center mb-1 font-medium text-muted-foreground"
                >
                  <span>Profile Completeness</span>
                  <span class="text-foreground font-semibold"
                    >{{ application?.profileCompleteness || 0 }}%</span
                  >
                </div>
                <Progress
                  :model-value="application?.profileCompleteness || 0"
                  class="h-1.5"
                />
              </CardContent>
            </Card>

            <!-- Notifications Card -->
            <Card class="shadow-sm">
              <CardHeader
                class="flex flex-row items-center justify-between space-y-0 pb-2 pt-4 px-4"
              >
                <CardTitle class="text-sm font-medium">Notifications</CardTitle>
                <div class="relative">
                  <Icon
                    icon="lucide:bell"
                    class="h-5 w-5 text-muted-foreground"
                  />
                  <span
                    v-if="application?.notificationsCount > 0"
                    class="absolute -top-1 -right-1 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-background"
                  />
                </div>
              </CardHeader>
              <CardContent class="px-4 pb-4">
                <p class="text-xs text-muted-foreground mb-3">
                  You have {{ application?.notificationsCount || 0 }} unread
                  notifications.
                </p>
                <Button
                  :as="Link"
                  href="#"
                  variant="secondary"
                  size="sm"
                  class="w-full"
                >
                  <!-- Adjust route -->
                  View Notifications
                </Button>
              </CardContent>
            </Card>

            <!-- Quick Links Card -->
            <Card class="shadow-sm bg-muted/30 dark:bg-card">
              <CardHeader class="pb-2 pt-4 px-4">
                <CardTitle class="text-sm font-medium"
                  >Resources & Support</CardTitle
                >
              </CardHeader>
              <CardContent
                class="px-4 pb-4 flex flex-col items-start space-y-1"
              >
                <!-- Using Buttons with link variant for consistency -->
                <Button
                  as="a"
                  href="#"
                  variant="link"
                  class="h-auto px-0 py-0.5 text-sm text-muted-foreground hover:text-primary"
                >
                  <Icon icon="lucide:file-text" class="mr-1.5 h-4 w-4" /> My
                  Documents
                </Button>
                <Button
                  as="a"
                  href="#"
                  variant="link"
                  class="h-auto px-0 py-0.5 text-sm text-muted-foreground hover:text-primary"
                >
                  <Icon icon="lucide:life-buoy" class="mr-1.5 h-4 w-4" /> Help &
                  FAQs
                </Button>
                <Button
                  as="a"
                  href="#"
                  variant="link"
                  class="h-auto px-0 py-0.5 text-sm text-muted-foreground hover:text-primary"
                >
                  <Icon icon="lucide:mail" class="mr-1.5 h-4 w-4" /> Contact
                  Support
                </Button>
              </CardContent>
            </Card>
          </div>
          <!-- === END Right Sidebar Column === -->

        </div>
        <!-- End Main Grid -->
      </div>

      <!-- Main content -->
      <div class="container max-w-6xl mx-auto px-4 -mt-8">
        <!-- Mobile progress tracker -->
        <div class="md:hidden mb-6">
          <Card>
            <CardContent class="pt-6 pb-4">
              <p class="text-sm font-medium mb-1.5 flex items-center justify-between">
                <span>Application Progress</span>
                <span>{{ progressPercentage }}%</span>
              </p>
              <Progress :value="progressPercentage" :class="progressColor" class="h-2" />
              <div class="flex justify-between text-sm mt-2 text-muted-foreground">
                <span>{{ uploadedRequiredCount }} of {{ requiredDocumentsCount }} documents</span>
                <span v-if="progressPercentage === 100">Complete</span>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Action cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <!-- Application status card -->
          <Card class="shadow-sm hover:shadow-md transition-shadow border-l-4" :class="{
            'border-l-warning': applicationStatus.variant === 'warning',
            'border-l-info': applicationStatus.variant === 'info',
            'border-l-success': applicationStatus.variant === 'success',
            'border-l-destructive': applicationStatus.variant === 'destructive',
            'border-l-secondary': applicationStatus.variant === 'secondary',
          }">
            <CardHeader class="pb-2">
              <CardTitle class="text-lg flex items-center">
                <Icon icon="lucide:clipboard-list" class="h-5 w-5 mr-2" />
                Application Status
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="flex items-center gap-4 mb-4">
                <div class="h-12 w-12 bg-muted rounded-full flex items-center justify-center">
                  <Icon :icon="applicationStatus.icon" class="h-6 w-6" :class="{
                    'text-warning': applicationStatus.variant === 'warning',
                    'text-info': applicationStatus.variant === 'info',
                    'text-success': applicationStatus.variant === 'success',
                    'text-destructive': applicationStatus.variant === 'destructive',
                    'text-muted-foreground': applicationStatus.variant === 'secondary',
                  }"/>
                </div>
                <div>
                  <h3 class="font-semibold">{{ applicationStatus.label }}</h3>
                  <p class="text-sm text-muted-foreground">
                    {{ application?.status === 'incomplete'
                      ? 'Upload required documents to complete your application'
                      : application?.status === 'submitted'
                        ? 'Your application is under review'
                        : application?.status === 'approved'
                          ? 'Your application has been approved!'
                          : application?.status === 'rejected'
                            ? application.rejection_reason || 'Application not approved'
                            : 'Waiting for processing' }}
                  </p>
                </div>
              </div>

              <div v-if="application?.status === 'incomplete' && progressPercentage < 100">
                <Button variant="default" class="w-full" @click="showDocumentUploader = true">
                  <Icon icon="lucide:file-plus" class="mr-2 h-4 w-4" />
                  Continue Application
                </Button>
              </div>
              <div v-else-if="application?.status === 'incomplete' && progressPercentage === 100">
                <Button variant="default" class="w-full" @click="submitApplication">
                  <Icon icon="lucide:send" class="mr-2 h-4 w-4" />
                  Submit Application
                </Button>
              </div>
            </CardContent>
          </Card>

          <!-- Scholarships card -->
          <Card class="shadow-sm hover:shadow-md transition-shadow border-l-4 border-l-primary">
            <CardHeader class="pb-2">
              <CardTitle class="text-lg flex items-center">
                <Icon icon="lucide:award" class="h-5 w-5 mr-2" />
                Scholarships
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="mb-3">
                <div class="flex items-center gap-2 mb-2">
                  <Badge variant="outline">{{ eligibleScholarships.length }} Available</Badge>
                  <Badge variant="outline">{{ activeScholarships?.length || 0 }} Applied</Badge>
                </div>
                <p class="text-sm text-muted-foreground">
                  {{ eligibleScholarships.length > 0
                    ? 'Explore scholarships that match your profile'
                    : 'No new scholarships available right now' }}
                </p>
              </div>

              <div v-if="eligibleScholarships.length > 0" class="mb-4">
                <div v-for="(scholarship, index) in eligibleScholarships.slice(0, 1)" :key="scholarship.id"
                  class="rounded-lg border p-3 mb-2">
                  <div class="flex justify-between items-start">
                    <h4 class="font-medium">{{ scholarship.name }}</h4>
                    <Badge variant="outline">{{ getDaysRemaining(scholarship.application_deadline) }} days left</Badge>
                  </div>
                  <p class="text-sm text-muted-foreground my-1 line-clamp-2">
                    {{ scholarship.description }}
                  </p>
                </div>
                <p v-if="eligibleScholarships.length > 1" class="text-sm text-muted-foreground mb-2">
                  + {{ eligibleScholarships.length - 1 }} more scholarships available
                </p>
              </div>

              <Button variant="outline" class="w-full" :disabled="progressPercentage < 100 && eligibleScholarships.length > 0"
                @click="progressPercentage < 100 && eligibleScholarships.length > 0 ? showDocumentUploader = true : null">
                <Icon icon="lucide:search" class="mr-2 h-4 w-4" />
                {{ progressPercentage < 100 && eligibleScholarships.length > 0
                  ? 'Complete Profile to Apply'
                  : eligibleScholarships.length > 0
                    ? 'Browse Scholarships'
                    : 'Check Back Later' }}
              </Button>
            </CardContent>
          </Card>

          <!-- Documents card -->
          <Card class="shadow-sm hover:shadow-md transition-shadow border-l-4 border-l-accent">
            <CardHeader class="pb-2">
              <CardTitle class="text-lg flex items-center">
                <Icon icon="lucide:file-text" class="h-5 w-5 mr-2" />
                Documents
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="mb-3">
                <div class="flex items-center gap-2 mb-2">
                  <Badge variant="outline">{{ uploadedRequiredCount }}/{{ requiredDocumentsCount }} Required</Badge>
                  <Badge variant="outline">{{ documents?.length || 0 }} Uploaded</Badge>
                </div>
                <p class="text-sm text-muted-foreground">
                  {{ uploadedRequiredCount < requiredDocumentsCount
                    ? 'Upload required documents to complete your application'
                    : 'All required documents uploaded' }}
                </p>
              </div>

              <div v-if="documentTypes?.length > 0" class="mb-4">
                <div class="space-y-2">
                  <div v-for="docType in documentTypes.filter(d => d.required).slice(0, 2)" :key="docType.type"
                    class="flex items-center justify-between rounded-lg border p-2">
                    <div class="flex items-center gap-2">
                      <Icon :icon="hasDocument(docType.type) ? 'lucide:check-circle' : 'lucide:file'"
                        :class="hasDocument(docType.type) ? 'text-success' : 'text-muted-foreground'"
                        class="h-4 w-4" />
                      <span class="text-sm">{{ docType.label }}</span>
                    </div>
                    <Badge v-if="hasDocument(docType.type)" variant="success" class="text-xs">Uploaded</Badge>
                    <Badge v-else variant="outline" class="text-xs">Required</Badge>
                  </div>
                </div>
                <p v-if="documentTypes.filter(d => d.required).length > 2" class="text-sm text-muted-foreground mt-2">
                  + {{ documentTypes.filter(d => d.required).length - 2 }} more required documents
                </p>
              </div>

              <Button :variant="uploadedRequiredCount < requiredDocumentsCount ? 'default' : 'outline'"
                class="w-full" @click="showDocumentUploader = true">
                <Icon icon="lucide:file-plus" class="mr-2 h-4 w-4" />
                {{ uploadedRequiredCount < requiredDocumentsCount ? 'Upload Documents' : 'Manage Documents' }}
              </Button>
            </CardContent>
          </Card>
        </div>

        <!-- Content Sections -->
        <div class="grid lg:grid-cols-3 gap-6">
          <!-- Left column: Personal Info -->
          <div class="lg:col-span-1 space-y-6">
            <Card class="shadow-sm">
              <CardHeader>
                <CardTitle class="text-lg flex items-center">
                  <Icon icon="lucide:user" class="h-5 w-5 mr-2" />
                  Personal Information
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="space-y-4">
                  <div class="space-y-1.5">
                    <h4 class="text-sm font-medium text-muted-foreground">Full Name</h4>
                    <p>{{ scholar.first_name }} {{ scholar.middle_name }} {{ scholar.last_name }}</p>
                  </div>
                  <div class="space-y-1.5">
                    <h4 class="text-sm font-medium text-muted-foreground">Email</h4>
                    <p>{{ scholar.email }}</p>
                  </div>
                  <div class="space-y-1.5">
                    <h4 class="text-sm font-medium text-muted-foreground">Contact Number</h4>
                    <p>{{ scholar.contact_number }}</p>
                  </div>
                  <div class="space-y-1.5">
                    <h4 class="text-sm font-medium text-muted-foreground">Gender</h4>
                    <p class="capitalize">{{ scholar.gender }}</p>
                  </div>
                  <div class="space-y-1.5">
                    <h4 class="text-sm font-medium text-muted-foreground">Birth Date</h4>
                    <p>{{ formatDate(scholar.birth_date) }}</p>
                  </div>
                  <div class="space-y-1.5">
                    <h4 class="text-sm font-medium text-muted-foreground">Address</h4>
                    <p>{{ scholar.address }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card v-if="activeScholarships && activeScholarships.length > 0" class="shadow-sm">
              <CardHeader>
                <CardTitle class="text-lg flex items-center">
                  <Icon icon="lucide:award" class="h-5 w-5 mr-2" />
                  Your Scholarships
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="space-y-4">
                  <div v-for="scholarship in activeScholarships" :key="scholarship.id"
                    class="border rounded-lg p-3 space-y-2">
                    <div class="flex justify-between items-start">
                      <h3 class="font-medium">{{ scholarship.name }}</h3>
                      <Badge :variant="
                        scholarship.pivot.status === 'active' ? 'success' :
                        scholarship.pivot.status === 'pending' ? 'warning' : 'secondary'
                      ">
                        {{ scholarship.pivot.status }}
                      </Badge>
                    </div>
                    <div class="flex items-center text-sm text-muted-foreground gap-2">
                      <Icon icon="lucide:calendar" class="h-4 w-4" />
                      <span>{{ formatDate(scholarship.pivot.start_date) || 'Pending' }}</span>
                    </div>
                    <div v-if="scholarship.pivot.remarks" class="text-sm p-2 bg-muted rounded">
                      {{ scholarship.pivot.remarks }}
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Middle and right columns: Scholarships and Documents -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Scholarships list -->
            <Card class="shadow-sm">
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-lg flex items-center">
                  <Icon icon="lucide:award" class="h-5 w-5 mr-2" />
                  Available Scholarships
                </CardTitle>
                <Badge>{{ eligibleScholarships.length }} Available</Badge>
              </CardHeader>
              <CardContent class="pt-4">
                <div v-if="eligibleScholarships.length === 0" class="text-center py-6">
                  <div class="mx-auto w-12 h-12 rounded-full bg-muted flex items-center justify-center mb-3">
                    <Icon icon="lucide:search" class="h-6 w-6 text-muted-foreground" />
                  </div>
                  <h3 class="font-medium mb-1">No Scholarships Available</h3>
                  <p class="text-sm text-muted-foreground">
                    There are currently no new scholarships for you to apply to.
                    Check back later or contact the scholarship office.
                  </p>
                </div>

                <div v-else class="space-y-4">
                  <div v-for="scholarship in eligibleScholarships" :key="scholarship.id"
                    class="border rounded-lg overflow-hidden">
                    <div class="h-1.5 bg-primary w-full"></div>
                    <div class="p-4 space-y-3">
                      <div class="flex justify-between items-start">
                        <h3 class="font-medium">{{ scholarship.name }}</h3>
                        <Badge variant="outline">
                          {{ getDaysRemaining(scholarship.application_deadline) }} days left
                        </Badge>
                      </div>
                      <p class="text-sm text-muted-foreground">
                        {{ scholarship.description.substring(0, 150) }}{{ scholarship.description.length > 150 ? "..." : "" }}
                      </p>
                      <div class="flex items-center gap-4 text-sm text-muted-foreground">
                        <div class="flex items-center gap-1">
                          <Icon icon="lucide:calendar" class="h-4 w-4" />
                          <span>{{ formatDate(scholarship.application_deadline) }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                          <Icon icon="lucide:list-checks" class="h-4 w-4" />
                          <span>
                            {{ scholarship.requirements ? Object.keys(scholarship.requirements).length : 0 }} requirements
                          </span>
                        </div>
                      </div>
                      <div class="pt-2">
                        <Button class="w-full" @click="selectScholarship(scholarship)"
                          :disabled="!canSubmit">
                          {{ canSubmit ? 'View Details' : 'Complete Profile to Apply' }}
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Documents list -->
            <Card v-if="documents && documents.length > 0" class="shadow-sm">
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-lg flex items-center">
                  <Icon icon="lucide:file-text" class="h-5 w-5 mr-2" />
                  Uploaded Documents
                </CardTitle>
                <Button variant="ghost" size="sm" @click="showDocumentUploader = true" class="h-8">
                  <Icon icon="lucide:plus" class="h-4 w-4 mr-1" />
                  Add More
                </Button>
              </CardHeader>
              <CardContent class="pt-4">
                <div class="space-y-3">
                  <div v-for="document in documents" :key="document.id"
                    class="flex items-center justify-between p-3 border rounded-lg">
                    <div class="flex items-center gap-3">
                      <div class="h-9 w-9 bg-muted rounded-full flex items-center justify-center">
                        <Icon
                          :icon="document.type.includes('certificate') ? 'lucide:certificate' :
                                document.type.includes('id') ? 'lucide:id-card' : 'lucide:file-text'"
                          class="h-5 w-5 text-muted-foreground" />
                      </div>
                      <div>
                        <h4 class="font-medium">
                          {{ documentTypes.find(d => d.type === document.type)?.label || document.type }}
                        </h4>
                        <p class="text-xs text-muted-foreground">
                          Uploaded on {{ formatDate(document.created_at) }}
                        </p>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <Badge :variant="
                        document.status === 'approved' ? 'success' :
                        document.status === 'rejected' ? 'destructive' : 'secondary'
                      ">
                        {{ document.status }}
                      </Badge>
                      <Button variant="ghost" size="sm" class="h-8 aspect-square p-0">
                        <Icon icon="lucide:eye" class="h-4 w-4" />
                      </Button>
                      <Button variant="ghost" size="sm" class="h-8 aspect-square p-0"
                                              @click="openFileUpload(document.type)">
                                              <Icon icon="lucide:refresh-cw" class="h-4 w-4" />
                                            </Button>
                                          </div>
                                        </div>
                                      </div>
                                    </CardContent>
                                  </Card>

                                  <!-- Extra helpful card when no documents uploaded yet -->
                                  <Card v-if="documents.length === 0" class="shadow-sm border-dashed bg-muted/50 border-muted">
                                    <CardContent class="py-8">
                                      <div class="text-center">
                                        <div class="mx-auto w-16 h-16 rounded-full bg-muted flex items-center justify-center mb-4">
                                          <Icon icon="lucide:file-plus" class="h-8 w-8 text-muted-foreground" />
                                        </div>
                                        <h3 class="text-lg font-medium mb-2">No Documents Uploaded Yet</h3>
                                        <p class="text-sm text-muted-foreground max-w-md mx-auto mb-6">
                                          Upload the required documents to complete your application. This will allow you to
                                          apply for scholarships and increase your chances of being approved.
                                        </p>
                                        <Button @click="showDocumentUploader = true">
                                          <Icon icon="lucide:file-plus" class="mr-2 h-4 w-4" />
                                          Upload Documents
                                        </Button>
                                      </div>
                                    </CardContent>
                                  </Card>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Scholarship Details Dialog -->
                          <Dialog v-model:open="showScholarshipDetails">
                            <DialogContent class="sm:max-w-lg">
                              <DialogHeader>
                                <DialogTitle class="flex items-center">
                                  <Icon icon="lucide:award" class="mr-2 h-5 w-5 text-primary" />
                                  {{ selectedScholarship?.name }}
                                </DialogTitle>
                                <DialogDescription>Scholarship Details</DialogDescription>
                              </DialogHeader>

                              <div class="space-y-5 py-4">
                                <div>
                                  <h3 class="text-sm font-medium text-muted-foreground mb-2">About This Scholarship</h3>
                                  <p>{{ selectedScholarship?.description }}</p>
                                </div>

                                <Separator />

                                <div class="grid grid-cols-2 gap-4">
                                  <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-2">Application Deadline</h3>
                                    <div class="flex items-center gap-2">
                                      <div class="h-9 w-9 bg-muted rounded-full flex items-center justify-center">
                                        <Icon icon="lucide:calendar" class="h-5 w-5 text-primary" />
                                      </div>
                                      <div>
                                        <p class="font-medium">{{ formatDate(selectedScholarship?.application_deadline) }}</p>
                                        <Badge variant="outline" class="mt-1">
                                          {{ getDaysRemaining(selectedScholarship?.application_deadline) }} days left
                                        </Badge>
                                      </div>
                                    </div>
                                  </div>

                                  <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-2">Scholarship Status</h3>
                                    <div class="flex items-center gap-2">
                                      <div class="h-9 w-9 bg-muted rounded-full flex items-center justify-center">
                                        <Icon icon="lucide:check-circle" class="h-5 w-5 text-success" />
                                      </div>
                                      <div>
                                        <p class="font-medium">Active</p>
                                        <p class="text-xs text-muted-foreground">Currently accepting applications</p>
                                      </div>
                                    </div>
                                  </div>
                                </div>

                                <Separator />

                                <div>
                                  <h3 class="text-sm font-medium text-muted-foreground mb-2">Requirements</h3>
                                  <ul class="space-y-2">
                                    <li v-for="(requirement, index) in selectedScholarship?.requirements" :key="index"
                                      class="flex items-start gap-2 p-3 bg-muted/50 rounded-lg">
                                      <Icon icon="lucide:check-circle" class="h-5 w-5 text-success shrink-0 mt-0.5" />
                                      <span>{{ requirement }}</span>
                                    </li>
                                    <li v-if="!selectedScholarship?.requirements || Object.keys(selectedScholarship?.requirements || {}).length === 0"
                                      class="text-sm text-muted-foreground italic p-3 bg-muted/50 rounded-lg">
                                      No specific requirements listed beyond the standard application documents.
                                    </li>
                                  </ul>
                                </div>

                                <Alert v-if="!canSubmit" variant="warning">
                                  <Icon icon="lucide:alert-triangle" class="h-4 w-4" />
                                  <AlertTitle>Complete Your Application First</AlertTitle>
                                  <AlertDescription>
                                    You need to upload all required documents before applying for this scholarship.
                                    <Button
                                      variant="link"
                                      class="p-0 h-auto font-normal"
                                      @click="showScholarshipDetails = false; showDocumentUploader = true"
                                    >
                                      Go to document uploads
                                    </Button>
                                  </AlertDescription>
                                </Alert>
                              </div>

                              <DialogFooter>
                                <Button variant="outline" @click="showScholarshipDetails = false">
                                  Cancel
                                </Button>
                                <Button @click="applyForScholarship()" :disabled="!canSubmit">
                                  <Icon icon="lucide:check-circle" class="mr-2 h-4 w-4" />
                                  Apply for Scholarship
                                </Button>
                              </DialogFooter>
                            </DialogContent>
                          </Dialog>

                          <!-- Document Uploader Sheet -->
                          <Sheet v-model:open="showDocumentUploader" position="right" class="w-full sm:max-w-lg">
                            <SheetContent>
                              <SheetHeader>
                                <SheetTitle class="flex items-center">
                                  <Icon icon="lucide:file-plus" class="mr-2 h-5 w-5 text-primary" />
                                  Document Upload
                                </SheetTitle>
                                <SheetDescription>
                                  Upload the required documents to complete your application
                                </SheetDescription>
                              </SheetHeader>

                              <div class="py-6">
                                <div class="mb-6">
                                  <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-medium">Upload Progress</h3>
                                    <Badge variant="outline">{{ uploadedRequiredCount }}/{{ requiredDocumentsCount }} Required</Badge>
                                  </div>
                                  <Progress :value="progressPercentage" :class="progressColor" class="h-2 mb-1" />
                                  <p class="text-xs text-muted-foreground">
                                    {{ progressPercentage }}% complete -
                                    {{ progressPercentage < 100
                                      ? `Upload ${requiredDocumentsCount - uploadedRequiredCount} more required document(s)`
                                      : 'All required documents uploaded' }}
                                  </p>
                                </div>

                                <div class="space-y-5 mb-6">
                                  <h3 class="font-medium border-b pb-2">Required Documents</h3>
                                  <Accordion type="single" collapsible class="w-full">
                                    <AccordionItem v-for="docType in documentTypes.filter(d => d.required)" :key="docType.type"
                                      :value="docType.type">
                                      <AccordionTrigger class="py-3">
                                        <div class="flex items-center gap-3">
                                          <div class="h-9 w-9 rounded-full flex items-center justify-center"
                                            :class="hasDocument(docType.type) ? 'bg-success/10' : 'bg-muted'">
                                            <Icon
                                              :icon="hasDocument(docType.type) ? 'lucide:check' : 'lucide:file'"
                                              :class="hasDocument(docType.type) ? 'text-success' : 'text-muted-foreground'"
                                              class="h-5 w-5" />
                                          </div>
                                          <div class="text-left">
                                            <span class="text-base font-medium">{{ docType.label }}</span>
                                            <div class="flex items-center gap-2">
                                              <Badge v-if="hasDocument(docType.type)" variant="success" class="text-xs mr-1">Uploaded</Badge>
                                              <Badge variant="outline" class="text-xs">Required</Badge>
                                            </div>
                                          </div>
                                        </div>
                                      </AccordionTrigger>
                                      <AccordionContent class="px-3 pb-4">
                                        <p class="text-sm text-muted-foreground mb-4">
                                          {{ docType.description }}
                                        </p>

                                        <div v-if="hasDocument(docType.type)" class="mb-4 p-3 bg-muted/50 rounded-lg">
                                          <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium">Current document</p>
                                            <Badge
                                              :variant="
                                                documents.find(d => d.type === docType.type)?.status === 'approved' ? 'success' :
                                                documents.find(d => d.type === docType.type)?.status === 'rejected' ? 'destructive' :
                                                'secondary'
                                              "
                                            >
                                              {{ documents.find(d => d.type === docType.type)?.status || 'Pending' }}
                                            </Badge>
                                          </div>
                                          <p class="text-xs text-muted-foreground mt-1">
                                            Uploaded on {{ formatDate(documents.find(d => d.type === docType.type)?.created_at) }}
                                          </p>
                                        </div>

                                        <div class="flex gap-2">
                                          <Button
                                            variant="outline"
                                            @click="openFileUpload(docType.type)"
                                            :disabled="uploadingFile && currentDocumentType === docType.type">
                                            <Icon
                                              v-if="uploadingFile && currentDocumentType === docType.type"
                                              icon="lucide:loader-2"
                                              class="mr-2 h-4 w-4 animate-spin" />
                                            <Icon
                                              v-else-if="hasDocument(docType.type)"
                                              icon="lucide:refresh-cw"
                                              class="mr-2 h-4 w-4" />
                                            <Icon v-else icon="lucide:upload" class="mr-2 h-4 w-4" />
                                            {{ hasDocument(docType.type) ? 'Replace File' : 'Upload File' }}
                                          </Button>
                                          <Button variant="ghost" size="icon" v-if="hasDocument(docType.type)">
                                            <Icon icon="lucide:eye" class="h-4 w-4" />
                                          </Button>
                                        </div>
                                      </AccordionContent>
                                    </AccordionItem>
                                  </Accordion>
                                </div>

                                <div class="space-y-5">
                                  <h3 class="font-medium border-b pb-2">Optional Documents</h3>
                                  <Accordion type="single" collapsible class="w-full">
                                    <AccordionItem v-for="docType in documentTypes.filter(d => !d.required)" :key="docType.type"
                                      :value="docType.type">
                                      <AccordionTrigger class="py-3">
                                        <div class="flex items-center gap-3">
                                          <div class="h-9 w-9 rounded-full flex items-center justify-center"
                                            :class="hasDocument(docType.type) ? 'bg-success/10' : 'bg-muted'">
                                            <Icon
                                              :icon="hasDocument(docType.type) ? 'lucide:check' : 'lucide:file'"
                                              :class="hasDocument(docType.type) ? 'text-success' : 'text-muted-foreground'"
                                              class="h-5 w-5" />
                                          </div>
                                          <div class="text-left">
                                            <span class="text-base font-medium">{{ docType.label }}</span>
                                            <div class="flex items-center gap-2">
                                              <Badge v-if="hasDocument(docType.type)" variant="success" class="text-xs mr-1">Uploaded</Badge>
                                              <Badge variant="outline" class="text-xs">Optional</Badge>
                                            </div>
                                          </div>
                                        </div>
                                      </AccordionTrigger>
                                      <AccordionContent class="px-3 pb-4">
                                        <p class="text-sm text-muted-foreground mb-4">
                                          {{ docType.description }}
                                        </p>

                                        <div v-if="hasDocument(docType.type)" class="mb-4 p-3 bg-muted/50 rounded-lg">
                                          <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium">Current document</p>
                                            <Badge
                                              :variant="
                                                documents.find(d => d.type === docType.type)?.status === 'approved' ? 'success' :
                                                documents.find(d => d.type === docType.type)?.status === 'rejected' ? 'destructive' :
                                                'secondary'
                                              "
                                            >
                                              {{ documents.find(d => d.type === docType.type)?.status || 'Pending' }}
                                            </Badge>
                                          </div>
                                          <p class="text-xs text-muted-foreground mt-1">
                                            Uploaded on {{ formatDate(documents.find(d => d.type === docType.type)?.created_at) }}
                                          </p>
                                        </div>

                                        <div class="flex gap-2">
                                          <Button
                                            variant="outline"
                                            @click="openFileUpload(docType.type)"
                                            :disabled="uploadingFile && currentDocumentType === docType.type">
                                            <Icon
                                              v-if="uploadingFile && currentDocumentType === docType.type"
                                              icon="lucide:loader-2"
                                              class="mr-2 h-4 w-4 animate-spin" />
                                            <Icon
                                              v-else-if="hasDocument(docType.type)"
                                              icon="lucide:refresh-cw"
                                              class="mr-2 h-4 w-4" />
                                            <Icon v-else icon="lucide:upload" class="mr-2 h-4 w-4" />
                                            {{ hasDocument(docType.type) ? 'Replace File' : 'Upload File' }}
                                          </Button>
                                          <Button variant="ghost" size="icon" v-if="hasDocument(docType.type)">
                                            <Icon icon="lucide:eye" class="h-4 w-4" />
                                          </Button>
                                        </div>
                                      </AccordionContent>
                                    </AccordionItem>
                                  </Accordion>
                                </div>

                                <Alert v-if="progressPercentage === 100 && application?.status === 'incomplete'" variant="info" class="mt-6">
                                  <Icon icon="lucide:check-circle" class="h-4 w-4" />
                                  <AlertTitle>All Required Documents Uploaded</AlertTitle>
                                  <AlertDescription>
                                    You've uploaded all required documents. You can now submit your application.
                                  </AlertDescription>
                                </Alert>
                              </div>

                              <div class="mt-2 flex gap-2 justify-end">
                                <Button variant="outline" @click="showDocumentUploader = false">
                                  Close
                                </Button>
                                <Button
                                  v-if="progressPercentage === 100 && application?.status === 'incomplete'"
                                  @click="submitApplication"
                                >
                                  <Icon icon="lucide:send" class="mr-2 h-4 w-4" />
                                  Submit Application
                                </Button>
                              </div>

                              <form
                                ref="uploadForm"
                                @submit.prevent="uploadDocument"
                                class="hidden"
                              >
                                <input
                                  ref="fileInput"
                                  type="file"
                                  name="file"
                                  @change="handleFileChange"
                                  accept=".pdf,.jpg,.jpeg,.png"
                                />
                                <input
                                  type="hidden"
                                  name="document_type"
                                  v-model="currentDocumentType"
                                />
                              </form>
                            </SheetContent>
                          </Sheet>

                          <!-- Help/Tour Dialog -->
                          <Dialog v-model:open="showTour">
                            <DialogContent class="sm:max-w-md">
                              <DialogHeader>
                                <DialogTitle class="flex items-center">
                                  <Icon icon="lucide:help-circle" class="mr-2 h-5 w-5 text-primary" />
                                  Welcome to Your Dashboard
                                </DialogTitle>
                                <DialogDescription>
                                  Here's a quick overview of your scholarship portal
                                </DialogDescription>
                              </DialogHeader>

                              <div class="py-4 space-y-4">
                                <div class="flex items-start gap-4">
                                  <div class="h-8 w-8 bg-primary/10 rounded-full flex items-center justify-center mt-0.5">
                                    <Icon icon="lucide:clipboard-list" class="h-4 w-4 text-primary" />
                                  </div>
                                  <div>
                                    <h3 class="font-medium">Application Status</h3>
                                    <p class="text-sm text-muted-foreground">
                                      Monitor your application progress and see what steps you need to complete.
                                    </p>
                                  </div>
                                </div>

                                <div class="flex items-start gap-4">
                                  <div class="h-8 w-8 bg-primary/10 rounded-full flex items-center justify-center mt-0.5">
                                    <Icon icon="lucide:file-text" class="h-4 w-4 text-primary" />
                                  </div>
                                  <div>
                                    <h3 class="font-medium">Document Upload</h3>
                                    <p class="text-sm text-muted-foreground">
                                      Submit your required documents to complete your application. You can also replace uploaded documents if needed.
                                    </p>
                                  </div>
                                </div>

                                <div class="flex items-start gap-4">
                                  <div class="h-8 w-8 bg-primary/10 rounded-full flex items-center justify-center mt-0.5">
                                    <Icon icon="lucide:award" class="h-4 w-4 text-primary" />
                                  </div>
                                  <div>
                                    <h3 class="font-medium">Browse Scholarships</h3>
                                    <p class="text-sm text-muted-foreground">
                                      Explore available scholarships and apply for those that match your profile.
                                    </p>
                                  </div>
                                </div>

                                <div class="flex items-start gap-4">
                                  <div class="h-8 w-8 bg-primary/10 rounded-full flex items-center justify-center mt-0.5">
                                    <Icon icon="lucide:user" class="h-4 w-4 text-primary" />
                                  </div>
                                  <div>
                                    <h3 class="font-medium">Your Profile</h3>
                                    <p class="text-sm text-muted-foreground">
                                      View your personal information and keep it up to date.
                                    </p>
                                  </div>
                                </div>
                              </div>

                              <DialogFooter>
                                <Button @click="showTour = false">
                                  <Icon icon="lucide:check" class="mr-2 h-4 w-4" />
                                  Got it, thanks!
                                </Button>
                              </DialogFooter>
                            </DialogContent>
                          </Dialog>
                        </AppLayout>
                      </template>

                      <style scoped>
                      .dashboard-container {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                        gap: 1.5rem;
                      }
                      </style>
