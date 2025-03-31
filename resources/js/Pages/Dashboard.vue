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
    </div>
  </AppLayout>
</template>
