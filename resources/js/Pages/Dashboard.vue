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
import { Progress } from "@/Components/shadcn/ui/progress";
import { Separator } from "@/Components/shadcn/ui/separator";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/Components/shadcn/ui/dialog";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetHeader,
  SheetTitle,
} from "@/Components/shadcn/ui/sheet";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/Components/shadcn/ui/accordion";

import { Icon } from "@iconify/vue";
import { usePage, Link, useForm } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue"; // Import ref and watch
import { toast } from "vue-sonner";

const page = usePage();
const user = computed(() => page.props.auth.user);
const appName = computed(() => page.props.appName || "PhilexScholar");

// --- Define Props ---
const props = defineProps({
  application: Object, // The user's main application (can be null if none)
  documents: Array, // Array of uploaded documents
  scholar: Object, // Scholar profile details (can be null)
  documentTypes: Array, // Definitions of required/optional docs
  availableScholarships: Array, // Scholarships open for application
  activeScholarships: Array, // Scholarships the user has applied for/is part of
});

// --- Ref for Dialogs/Sheets ---
const showScholarshipDetails = ref(false);
const selectedScholarship = ref(null);
const showDocumentUploader = ref(false);
const showTour = ref(false); // Optional tour state

// --- Upload Logic Refs ---
const uploadFormRef = ref(null);
const fileInputRef = ref(null);
const currentDocumentType = ref(null);
const uploadingFile = ref(false);

// --- Forms ---
const docUploadForm = useForm({
  document_type: null,
  file: null,
});

const appSubmitForm = useForm({});
const scholarshipApplyForm = useForm({
  scholarship_id: null,
});

// --- Computed Properties for Display ---

const userName = computed(
  () => props.scholar?.first_name || user.value?.name || "Scholar",
);
const userInitials = computed(() => {
  return (
    userName.value
      ?.match(/\b(\w)/g)
      ?.join("")
      .toUpperCase()
      .slice(0, 2) || "S"
  );
});

// Status Badge Variant Mapping
const getStatusVariant = (status) => {
  switch (status?.toLowerCase()) {
    case "approved":
      return "success";
    case "submitted":
    case "pending_review":
    case "pending":
      return "info"; // Consolidate pending states
    case "incomplete":
    case "draft":
      return "secondary"; // Treat incomplete/draft similarly
    case "needs_information":
    case "action_required":
      return "warning"; // Add aliases if needed
    case "rejected":
      return "destructive";
    default:
      return "outline"; // For null or 'not started'
  }
};

// Icon Mapping
const getStatusIcon = (status) => {
  switch (status?.toLowerCase()) {
    case "approved":
      return "lucide:check-circle-2";
    case "submitted":
    case "pending_review":
    case "pending":
      return "lucide:hourglass";
    case "incomplete":
    case "draft":
      return "lucide:pencil-line";
    case "needs_information":
    case "action_required":
      return "lucide:alert-circle";
    case "rejected":
      return "lucide:x-circle";
    default:
      return "lucide:file-plus-2";
  }
};

// *** ADDED: The missing computed property ***
const applicationStatus = computed(() => {
  // Use props.application directly
  const status = props.application?.status;

  if (!status || status === "not_started") {
    // Assuming 'not_started' or null means no application
    return {
      label: "Not Started",
      variant: getStatusVariant(null),
      icon: getStatusIcon(null),
      description: "Start your scholarship application process.",
    };
  }

  // Determine label and description based on status from props
  let label = "Unknown Status";
  let description = "Your application status is unclear.";

  switch (status) {
    case "draft":
    case "incomplete":
      label = "In Progress";
      description = "Complete your profile and upload required documents.";
      break;
    case "pending": // From scholarship pivot
    case "pending_review": // Maybe an application status
    case "submitted":
      label = "Under Review";
      description =
        "Your application has been submitted and is being reviewed.";
      break;
    case "approved":
      label = "Approved";
      description = "Congratulations! Your application has been approved.";
      break;
    case "rejected":
      label = "Rejected";
      description =
        props.application.rejection_reason ||
        "Your application was not approved at this time.";
      break;
    case "needs_information": // Example if you add this status
      label = "Action Required";
      description = "Additional information or documents are needed.";
      break;
    default:
      label =
        status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, " "); // Basic formatting
      description = `Current status: ${label}`;
  }

  return {
    label: label,
    variant: getStatusVariant(status),
    icon: getStatusIcon(status),
    description: description,
  };
});

// Primary Action Button Logic (using props)
const primaryAction = computed(() => {
  if (!props.application || props.application.status === "not_started")
    return {
      text: "Find Scholarships",
      href: route("scholarships.index"),
      variant: "default",
    };

  switch (props.application.status?.toLowerCase()) {
    case "draft":
    case "incomplete":
      return {
        text: "Continue Application",
        action: () => (showDocumentUploader.value = true),
        variant: "default",
      }; // Open sheet
    case "needs_information":
      return {
        text: "Upload Required Info",
        action: () => (showDocumentUploader.value = true),
        variant: "warning",
      };
    case "approved": // Maybe link to view award details or tasks
      // Find if there's an active scholarship linked
      const awarded = props.activeScholarships?.find(
        (s) => s.pivot.status === "active",
      );
      if (awarded) {
        return {
          text: "View Scholarship Details",
          href: "#",
          variant: "success",
        }; // Replace # with actual route if needed
      }
      // If approved but no active scholarship pivot yet (e.g., needs acceptance)
      return { text: "View Approval Details", href: "#", variant: "success" }; // Replace #
    // No primary action needed for submitted/rejected generally
    default:
      return null;
  }
});

// Secondary Action Button Logic (using props)
const secondaryAction = computed(() => {
  if (!props.application || props.application.status === "not_started")
    return null;
  // Always allow viewing details if an application exists
  return { text: "View Application Details", href: "#", variant: "outline" }; // Replace # with route('applications.show', props.application.id) maybe
});

// Format Currency
const formatCurrency = (value) => {
  if (value == null) return "N/A";
  // Assuming PHP currency, adjust as needed
  return new Intl.NumberFormat("en-PH", {
    style: "currency",
    currency: "PHP",
  }).format(value);
};

// Format Date
const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  try {
    return new Date(dateString).toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  } catch (e) {
    return "Invalid Date";
  }
};

// Calculate days remaining
const getDaysRemaining = (deadline) => {
  if (!deadline) return "N/A";
  const today = new Date();
  const deadlineDate = new Date(deadline);
  const diffTime = deadlineDate - today;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays > 0 ? diffDays : 0;
};

// --- Document Progress Calculation ---
const requiredDocumentsCount = computed(() => {
  return props.documentTypes?.filter((doc) => doc.required).length ?? 0;
});

const uploadedRequiredCount = computed(() => {
  if (!props.documents || !props.documentTypes) return 0;
  const requiredTypes = props.documentTypes
    .filter((doc) => doc.required)
    .map((doc) => doc.type);
  return props.documents.filter((doc) => requiredTypes.includes(doc.type))
    .length;
});

const progressPercentage = computed(() => {
  const reqCount = requiredDocumentsCount.value;
  if (reqCount === 0) return props.scholar ? 100 : 0; // If no required docs, progress is 100 if profile exists
  const uploadedCount = uploadedRequiredCount.value;
  return Math.round((uploadedCount / reqCount) * 100);
});

const progressColor = computed(() => {
  const percentage = progressPercentage.value;
  if (percentage === 100) return "bg-success";
  if (percentage > 50) return "bg-primary";
  return "bg-warning";
});

// Check if a specific document type exists in uploaded documents
const hasDocument = (docType) => {
  return props.documents?.some((doc) => doc.type === docType);
};

// --- Filters ---
// Filter available scholarships to exclude those already applied for
const eligibleScholarships = computed(() => {
  if (!props.availableScholarships) return [];
  const appliedIds = new Set(props.activeScholarships?.map((s) => s.id) ?? []);
  return props.availableScholarships.filter((s) => !appliedIds.has(s.id));
});

// --- Actions ---

// Open file input for specific document type
const openFileUpload = (docType) => {
  currentDocumentType.value = docType;
  docUploadForm.reset(); // Clear previous file if any
  docUploadForm.document_type = docType;
  fileInputRef.value?.click();
};

// Handle file selection
const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file && currentDocumentType.value) {
    docUploadForm.file = file;
    uploadDocument(); // Automatically upload on selection
  }
  // Reset file input visually
  if (event.target) event.target.value = null;
};

// Upload the document
const uploadDocument = () => {
  if (!docUploadForm.file || !docUploadForm.document_type) {
    toast.error("File or document type missing.");
    return;
  }
  uploadingFile.value = true;
  docUploadForm.post(route("dashboard.upload-document"), {
    // Use named route
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      toast.success(
        `${currentDocumentType.value.replace(/_/g, " ")} uploaded successfully!`,
      );
      docUploadForm.reset();
      currentDocumentType.value = null;
    },
    onError: (errors) => {
      console.error("Upload Error:", errors);
      const errorMsg =
        errors.file ||
        errors.document_type ||
        "Upload failed. Please check the file and try again.";
      toast.error(errorMsg);
    },
    onFinish: () => {
      uploadingFile.value = false;
    },
  });
};

// Submit the main application
const submitApplication = () => {
  if (progressPercentage.value < 100) {
    toast.error("Please upload all required documents first.");
    return;
  }
  appSubmitForm.post(route("dashboard.submit-application"), {
    // Use named route
    preserveScroll: true,
    onSuccess: () => {
      toast.success("Application submitted successfully!");
      showDocumentUploader.value = false; // Close sheet on success
    },
    onError: (errors) => {
      const errorMsg =
        errors.message ||
        page.props.errors?.error ||
        "Failed to submit application.";
      toast.error(errorMsg);
    },
  });
};

// Select a scholarship to view details
const selectScholarship = (scholarship) => {
  selectedScholarship.value = scholarship;
  showScholarshipDetails.value = true;
};

// Apply for the selected scholarship
const applyForScholarship = () => {
  if (!selectedScholarship.value) return;

  scholarshipApplyForm.scholarship_id = selectedScholarship.value.id;
  scholarshipApplyForm.post(route("dashboard.apply-scholarship"), {
    // Use named route
    preserveScroll: true,
    onSuccess: () => {
      toast.success(
        `Successfully applied for ${selectedScholarship.value.name}!`,
      );
      showScholarshipDetails.value = false; // Close dialog
    },
    onError: (errors) => {
      const errorMsg =
        errors.message ||
        page.props.errors?.error ||
        "Failed to apply for scholarship.";
      toast.error(errorMsg);
    },
  });
};

// Condition to check if user can submit application/apply for scholarships
const canSubmit = computed(() => {
  // Must have an application, and all required docs must be uploaded
  return props.application && progressPercentage.value === 100;
});

// --- Watch for Flash Messages ---
watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success) {
      toast.success(flash.success);
    }
    if (flash?.error) {
      toast.error(flash.error);
    }
  },
  { immediate: true },
); // Check immediately on component load
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
        <div class="px-4 sm:px-0 flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-semibold text-foreground">
              Welcome back, {{ userName }}!
            </h1>
            <p class="mt-1 text-sm text-muted-foreground">
              Here's an overview of your scholarship journey.
            </p>
          </div>
          <Button variant="outline" size="sm" @click="showTour = true">
            <Icon icon="lucide:help-circle" class="h-4 w-4 mr-1.5" />
            Quick Tour
          </Button>
        </div>

        <!-- Main Content Area -->
        <div class="px-4 sm:px-0">
          <!-- Top Row: Key Action Cards -->
          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6 lg:mb-8">
            <!-- Application status card -->
            <Card
              class="shadow-sm hover:shadow-md transition-shadow border-l-4"
              :class="{
                'border-l-warning': applicationStatus.variant === 'warning',
                'border-l-info': applicationStatus.variant === 'info',
                'border-l-success': applicationStatus.variant === 'success',
                'border-l-destructive':
                  applicationStatus.variant === 'destructive',
                'border-l-primary': applicationStatus.variant === 'secondary', // Use primary for in progress
                'border-l-muted': applicationStatus.variant === 'outline',
              }"
            >
              <CardHeader class="pb-2">
                <CardTitle
                  class="text-base font-medium flex items-center text-muted-foreground"
                >
                  <Icon icon="lucide:clipboard-list" class="h-4 w-4 mr-2" />
                  Application Status
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="flex items-center gap-3 mb-3">
                  <div
                    class="h-10 w-10 bg-muted rounded-full flex items-center justify-center flex-shrink-0"
                  >
                    <Icon
                      :icon="applicationStatus.icon"
                      class="h-5 w-5"
                      :class="{
                        'text-warning-foreground':
                          applicationStatus.variant === 'warning',
                        'text-info-foreground':
                          applicationStatus.variant === 'info',
                        'text-success-foreground':
                          applicationStatus.variant === 'success',
                        'text-destructive-foreground':
                          applicationStatus.variant === 'destructive',
                        'text-primary':
                          applicationStatus.variant === 'secondary',
                        'text-muted-foreground':
                          applicationStatus.variant === 'outline',
                      }"
                    />
                  </div>
                  <div>
                    <h3 class="font-semibold text-lg text-foreground">
                      {{ applicationStatus.label }}
                    </h3>
                    <p class="text-xs text-muted-foreground leading-tight">
                      {{ applicationStatus.description }}
                    </p>
                  </div>
                </div>

                <!-- Action Button based on Status -->
                <div v-if="primaryAction">
                  <Button
                    :variant="primaryAction.variant"
                    class="w-full"
                    @click="
                      primaryAction.action ? primaryAction.action() : null
                    "
                    :as="primaryAction.href ? Link : 'button'"
                    :href="primaryAction.href"
                    size="sm"
                  >
                    <Icon
                      :icon="getStatusIcon(props.application?.status)"
                      class="mr-1.5 h-4 w-4"
                    />
                    {{ primaryAction.text }}
                  </Button>
                </div>
                <div
                  v-else-if="
                    props.application?.status === 'submitted' ||
                    props.application?.status === 'pending_review'
                  "
                >
                  <p
                    class="text-xs text-center text-muted-foreground p-2 bg-muted rounded-md"
                  >
                    No immediate actions required. We'll notify you of updates.
                  </p>
                </div>
                <div v-else-if="props.application?.status === 'rejected'">
                  <p
                    class="text-xs text-center text-muted-foreground p-2 bg-destructive/10 rounded-md border border-destructive/30"
                  >
                    Review rejection details if available.
                  </p>
                </div>
              </CardContent>
            </Card>

            <!-- Scholarships card -->
            <Card
              class="shadow-sm hover:shadow-md transition-shadow border-l-4 border-l-primary"
            >
              <CardHeader class="pb-2">
                <CardTitle
                  class="text-base font-medium flex items-center text-muted-foreground"
                >
                  <Icon icon="lucide:award" class="h-4 w-4 mr-2" />
                  Available Scholarships
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="flex items-center gap-3 mb-3">
                  <div
                    class="h-10 w-10 bg-muted rounded-full flex items-center justify-center flex-shrink-0"
                  >
                    <Icon
                      icon="lucide:search-check"
                      class="h-5 w-5 text-primary"
                    />
                  </div>
                  <div>
                    <h3 class="font-semibold text-lg text-foreground">
                      {{ eligibleScholarships.length }}
                    </h3>
                    <p class="text-xs text-muted-foreground leading-tight">
                      {{
                        eligibleScholarships.length > 0
                          ? "Scholarships matching your profile"
                          : "No new opportunities now"
                      }}
                    </p>
                  </div>
                </div>
                <div class="mb-3 flex items-center gap-2 text-xs">
                  <Badge variant="outline"
                    >{{ eligibleScholarships.length }} Available</Badge
                  >
                  <Badge variant="outline"
                    >{{ activeScholarships?.length || 0 }} Applied</Badge
                  >
                </div>

                <Button
                  variant="outline"
                  class="w-full"
                  :disabled="!canSubmit && eligibleScholarships.length > 0"
                  @click="
                    !canSubmit && eligibleScholarships.length > 0
                      ? (showDocumentUploader = true)
                      : null /* TODO: Link to scholarship browse page */
                  "
                  size="sm"
                >
                  <Icon
                    :icon="
                      !canSubmit && eligibleScholarships.length > 0
                        ? 'lucide:lock'
                        : 'lucide:search'
                    "
                    class="mr-2 h-4 w-4"
                  />
                  {{
                    !canSubmit && eligibleScholarships.length > 0
                      ? "Complete Profile to Apply"
                      : eligibleScholarships.length > 0
                        ? "Browse Scholarships"
                        : "Check Back Later"
                  }}
                </Button>
              </CardContent>
            </Card>

            <!-- Documents card -->
            <Card
              class="shadow-sm hover:shadow-md transition-shadow border-l-4 border-l-accent"
            >
              <CardHeader class="pb-2">
                <CardTitle
                  class="text-base font-medium flex items-center text-muted-foreground"
                >
                  <Icon icon="lucide:file-text" class="h-4 w-4 mr-2" />
                  Document Status
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="flex items-center gap-3 mb-3">
                  <div
                    class="h-10 w-10 bg-muted rounded-full flex items-center justify-center flex-shrink-0"
                  >
                    <Icon
                      :icon="
                        progressPercentage === 100
                          ? 'lucide:check-check'
                          : 'lucide:file-warning'
                      "
                      class="h-5 w-5"
                      :class="
                        progressPercentage === 100
                          ? 'text-success'
                          : 'text-warning'
                      "
                    />
                  </div>
                  <div>
                    <h3 class="font-semibold text-lg text-foreground">
                      {{ progressPercentage }}%
                    </h3>
                    <p class="text-xs text-muted-foreground leading-tight">
                      {{
                        progressPercentage < 100
                          ? "Required documents pending"
                          : "All required documents uploaded"
                      }}
                    </p>
                  </div>
                </div>
                <div class="mb-3 flex items-center gap-2 text-xs">
                  <Badge variant="outline"
                    >{{ uploadedRequiredCount }}/{{
                      requiredDocumentsCount
                    }}
                    Required</Badge
                  >
                  <Badge variant="outline"
                    >{{ documents?.length || 0 }} Total Uploaded</Badge
                  >
                </div>

                <Button
                  :variant="
                    uploadedRequiredCount < requiredDocumentsCount
                      ? 'default'
                      : 'outline'
                  "
                  class="w-full"
                  @click="showDocumentUploader = true"
                  size="sm"
                >
                  <Icon
                    :icon="
                      uploadedRequiredCount < requiredDocumentsCount
                        ? 'lucide:file-plus'
                        : 'lucide:folder-cog'
                    "
                    class="mr-2 h-4 w-4"
                  />
                  {{
                    uploadedRequiredCount < requiredDocumentsCount
                      ? "Upload Documents"
                      : "Manage Documents"
                  }}
                </Button>
              </CardContent>
            </Card>
          </div>

          <!-- Bottom Row: Detailed Info -->
          <div class="grid lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Left Column: Personal & Active Scholarships -->
            <div class="lg:col-span-1 space-y-6 lg:space-y-8">
              <Card class="shadow-sm">
                <CardHeader>
                  <CardTitle class="text-base font-medium flex items-center">
                    <Icon
                      icon="lucide:user-circle"
                      class="h-4 w-4 mr-2 text-muted-foreground"
                    />
                    Personal Information
                  </CardTitle>
                </CardHeader>
                <CardContent class="text-sm space-y-2">
                  <div v-if="props.scholar">
                    <p>
                      <strong
                        class="text-muted-foreground font-normal w-20 inline-block"
                        >Name:</strong
                      >
                      {{ props.scholar.first_name }}
                      {{ props.scholar.last_name }}
                    </p>
                    <p>
                      <strong
                        class="text-muted-foreground font-normal w-20 inline-block"
                        >Email:</strong
                      >
                      {{ props.scholar.email }}
                    </p>
                    <p>
                      <strong
                        class="text-muted-foreground font-normal w-20 inline-block"
                        >Contact:</strong
                      >
                      {{ props.scholar.contact_number }}
                    </p>
                    <p>
                      <strong
                        class="text-muted-foreground font-normal w-20 inline-block"
                        >Gender:</strong
                      >
                      <span class="capitalize">{{ props.scholar.gender }}</span>
                    </p>
                    <p>
                      <strong
                        class="text-muted-foreground font-normal w-20 inline-block"
                        >Born:</strong
                      >
                      {{ formatDate(props.scholar.birth_date) }}
                    </p>
                    <p>
                      <strong
                        class="text-muted-foreground font-normal w-20 inline-block"
                        >Address:</strong
                      >
                      {{ props.scholar.address }}
                    </p>
                  </div>
                  <div
                    v-else
                    class="text-muted-foreground text-xs text-center py-4"
                  >
                    Scholar profile not found.
                  </div>
                </CardContent>
                <CardFooter class="border-t pt-3">
                  <Button
                    :as="Link"
                    :href="route('profile.show')"
                    variant="outline"
                    size="sm"
                  >
                    <Icon icon="lucide:edit-3" class="mr-1.5 h-3.5 w-3.5" />
                    Edit Profile
                  </Button>
                </CardFooter>
              </Card>

              <Card
                v-if="activeScholarships && activeScholarships.length > 0"
                class="shadow-sm"
              >
                <CardHeader>
                  <CardTitle class="text-base font-medium flex items-center">
                    <Icon
                      icon="lucide:graduation-cap"
                      class="h-4 w-4 mr-2 text-muted-foreground"
                    />
                    Your Active Scholarships
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div class="space-y-4">
                    <div
                      v-for="scholarship in activeScholarships"
                      :key="scholarship.id"
                      class="border rounded-lg p-3 space-y-2"
                    >
                      <div class="flex justify-between items-start">
                        <h3 class="font-medium text-sm">
                          {{ scholarship.name }}
                        </h3>
                        <Badge
                          size="sm"
                          :variant="
                            scholarship.pivot.status === 'active'
                              ? 'success'
                              : scholarship.pivot.status === 'pending'
                                ? 'warning'
                                : 'secondary'
                          "
                        >
                          {{ scholarship.pivot.status }}
                        </Badge>
                      </div>
                      <div
                        class="flex items-center text-xs text-muted-foreground gap-2"
                      >
                        <Icon icon="lucide:calendar-days" class="h-3.5 w-3.5" />
                        <span>{{
                          formatDate(scholarship.pivot.start_date) ||
                          "Pending Start"
                        }}</span>
                      </div>
                      <div
                        v-if="scholarship.pivot.remarks"
                        class="text-xs p-2 bg-muted rounded mt-1"
                      >
                        <strong class="block text-foreground mb-0.5"
                          >Remarks:</strong
                        >
                        {{ scholarship.pivot.remarks }}
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>

            <!-- Right Column: Available Scholarships & Documents Overview -->
            <div class="lg:col-span-2 space-y-6 lg:space-y-8">
              <!-- Available Scholarships List -->
              <Card class="shadow-sm">
                <CardHeader
                  class="flex flex-row items-center justify-between space-y-0 pb-2"
                >
                  <CardTitle class="text-base font-medium flex items-center">
                    <Icon
                      icon="lucide:list-plus"
                      class="h-4 w-4 mr-2 text-muted-foreground"
                    />
                    Available Opportunities
                  </CardTitle>
                  <Badge variant="secondary"
                    >{{ eligibleScholarships.length }} Available</Badge
                  >
                </CardHeader>
                <CardContent class="pt-4">
                  <div
                    v-if="eligibleScholarships.length === 0"
                    class="text-center py-8"
                  >
                    <div
                      class="mx-auto w-12 h-12 rounded-full bg-muted flex items-center justify-center mb-3"
                    >
                      <Icon
                        icon="lucide:folder-search"
                        class="h-6 w-6 text-muted-foreground"
                      />
                    </div>
                    <h3 class="font-medium mb-1 text-sm">
                      No New Scholarships
                    </h3>
                    <p class="text-xs text-muted-foreground max-w-xs mx-auto">
                      There are currently no new scholarships matching your
                      profile. Check back later!
                    </p>
                  </div>

                  <div v-else class="space-y-4">
                    <div
                      v-for="scholarship in eligibleScholarships.slice(0, 3)"
                      :key="scholarship.id"
                      class="border rounded-lg overflow-hidden transition-shadow hover:shadow-md"
                    >
                      <div class="p-4 space-y-2">
                        <div class="flex justify-between items-start gap-2">
                          <h3 class="font-semibold text-sm leading-tight">
                            {{ scholarship.name }}
                          </h3>
                          <Badge
                            variant="outline"
                            size="sm"
                            class="flex-shrink-0 whitespace-nowrap"
                          >
                            {{
                              getDaysRemaining(scholarship.application_deadline)
                            }}
                            days left
                          </Badge>
                        </div>
                        <p class="text-xs text-muted-foreground line-clamp-2">
                          {{ scholarship.description }}
                        </p>
                        <div
                          class="flex items-center gap-4 text-xs text-muted-foreground pt-1"
                        >
                          <div class="flex items-center gap-1">
                            <Icon
                              icon="lucide:calendar-clock"
                              class="h-3.5 w-3.5"
                            />
                            <span
                              >Deadline:
                              {{
                                formatDate(scholarship.application_deadline)
                              }}</span
                            >
                          </div>
                        </div>
                        <div class="pt-2">
                          <Button
                            size="sm"
                            class="w-full h-8"
                            @click="selectScholarship(scholarship)"
                            :disabled="!canSubmit"
                          >
                            <Icon
                              :icon="
                                canSubmit ? 'lucide:arrow-right' : 'lucide:lock'
                              "
                              class="mr-1.5 h-3.5 w-3.5"
                            />
                            {{
                              canSubmit
                                ? "View & Apply"
                                : "Complete Profile First"
                            }}
                          </Button>
                        </div>
                      </div>
                    </div>
                    <div
                      v-if="eligibleScholarships.length > 3"
                      class="text-center pt-2"
                    >
                      <Button variant="link" size="sm">
                        View All {{ eligibleScholarships.length }} Scholarships
                      </Button>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <!-- Documents Overview (optional detailed list) -->
              <Card v-if="documents && documents.length > 0" class="shadow-sm">
                <CardHeader
                  class="flex flex-row items-center justify-between space-y-0 pb-2"
                >
                  <CardTitle class="text-base font-medium flex items-center">
                    <Icon
                      icon="lucide:folder-check"
                      class="h-4 w-4 mr-2 text-muted-foreground"
                    />
                    Uploaded Documents
                  </CardTitle>
                  <Button
                    variant="secondary"
                    size="sm"
                    @click="showDocumentUploader = true"
                    class="h-7"
                  >
                    Manage
                  </Button>
                </CardHeader>
                <CardContent class="pt-4">
                  <div class="space-y-3">
                    <p
                      class="text-xs text-muted-foreground text-center py-4"
                      v-if="!documents || documents.length === 0"
                    >
                      No documents have been uploaded yet.
                    </p>
                    <div
                      v-else
                      v-for="document in documents.slice(0, 4)"
                      :key="document.id"
                      class="flex items-center justify-between p-2 border rounded-lg text-sm"
                    >
                      <div class="flex items-center gap-2">
                        <Icon
                          :icon="
                            hasDocument(document.type)
                              ? 'lucide:check-circle'
                              : 'lucide:file'
                          "
                          :class="
                            hasDocument(document.type)
                              ? 'text-success'
                              : 'text-muted-foreground'
                          "
                          class="h-4 w-4 flex-shrink-0"
                        />
                        <span
                          class="font-medium truncate"
                          :title="
                            documentTypes.find((d) => d.type === document.type)
                              ?.label || document.type
                          "
                        >
                          {{
                            documentTypes.find((d) => d.type === document.type)
                              ?.label || document.type.replace(/_/g, " ")
                          }}
                        </span>
                      </div>
                      <div class="flex items-center gap-1 flex-shrink-0">
                        <Badge
                          size="sm"
                          :variant="
                            document.status === 'approved'
                              ? 'success'
                              : document.status === 'rejected'
                                ? 'destructive'
                                : 'secondary'
                          "
                        >
                          {{ document.status }}
                        </Badge>
                        <Button
                          variant="ghost"
                          size="icon"
                          class="h-7 w-7"
                          title="View Document"
                        >
                          <Icon icon="lucide:eye" class="h-3.5 w-3.5" />
                        </Button>
                      </div>
                    </div>
                    <div v-if="documents.length > 4" class="text-center pt-2">
                      <Button
                        variant="link"
                        size="sm"
                        @click="showDocumentUploader = true"
                      >
                        View All {{ documents.length }} Documents
                      </Button>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scholarship Details Dialog (Copied from previous template, ensure props match) -->
    <Dialog v-model:open="showScholarshipDetails">
      <DialogContent class="sm:max-w-lg">
        <DialogHeader>
          <DialogTitle class="flex items-center gap-2">
            <Icon icon="lucide:award" class="h-5 w-5 text-primary" />
            {{ selectedScholarship?.name }}
          </DialogTitle>
          <DialogDescription>Scholarship Details</DialogDescription>
        </DialogHeader>

        <div class="space-y-4 py-4 max-h-[60vh] overflow-y-auto pr-2">
          <div>
            <h3
              class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1"
            >
              Description
            </h3>
            <p class="text-sm">
              {{
                selectedScholarship?.description || "No description provided."
              }}
            </p>
          </div>

          <Separator />

          <div class="grid grid-cols-2 gap-4">
            <div>
              <h3
                class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1"
              >
                Deadline
              </h3>
              <div class="flex items-center gap-2 text-sm">
                <Icon
                  icon="lucide:calendar-clock"
                  class="h-4 w-4 text-primary flex-shrink-0"
                />
                <div>
                  <p class="font-medium">
                    {{ formatDate(selectedScholarship?.application_deadline) }}
                  </p>
                  <p class="text-xs text-orange-600">
                    {{
                      getDaysRemaining(
                        selectedScholarship?.application_deadline,
                      )
                    }}
                    days left
                  </p>
                </div>
              </div>
            </div>
            <div>
              <h3
                class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1"
              >
                Status
              </h3>
              <div class="flex items-center gap-2 text-sm">
                <Icon
                  icon="lucide:check-circle-2"
                  class="h-4 w-4 text-success flex-shrink-0"
                />
                <div>
                  <p class="font-medium">Active</p>
                  <p class="text-xs text-muted-foreground">
                    Accepting applications
                  </p>
                </div>
              </div>
            </div>
          </div>

          <Separator />

          <div>
            <h3
              class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2"
            >
              Requirements
            </h3>
            <!-- Requirements for scholarship (if stored on scholarship model) -->
            <ul
              v-if="
                selectedScholarship?.requirements &&
                Object.keys(selectedScholarship.requirements).length > 0
              "
              class="space-y-1.5 text-sm list-disc list-inside pl-1"
            >
              <li
                v-for="(req, key) in selectedScholarship.requirements"
                :key="key"
              >
                {{ req }}
                <!-- Adjust if requirements are structured differently -->
              </li>
            </ul>
            <!-- If requirements come from documentTypes -->
            <ul v-else class="space-y-1.5 text-sm">
              <li
                v-for="docType in documentTypes.filter((d) => d.required)"
                :key="docType.type"
                class="flex items-center gap-1.5"
              >
                <Icon
                  :icon="
                    hasDocument(docType.type)
                      ? 'lucide:check'
                      : 'lucide:circle-dashed'
                  "
                  :class="
                    hasDocument(docType.type)
                      ? 'text-success'
                      : 'text-muted-foreground'
                  "
                  class="h-3.5 w-3.5 flex-shrink-0"
                />
                {{ docType.label }} (Required General Document)
              </li>
              <li
                v-for="docType in documentTypes.filter((d) => !d.required)"
                :key="docType.type"
                class="flex items-center gap-1.5 text-muted-foreground"
              >
                <Icon icon="lucide:circle" class="h-3.5 w-3.5 flex-shrink-0" />
                {{ docType.label }} (Optional General Document)
              </li>
            </ul>
          </div>

          <Alert v-if="!canSubmit" variant="warning">
            <Icon icon="lucide:alert-triangle" class="h-4 w-4" />
            <AlertTitle>Application Incomplete</AlertTitle>
            <AlertDescription>
              You must upload all required documents before applying.
              <Button
                variant="link"
                class="p-0 h-auto font-normal text-warning-foreground underline"
                @click="
                  showScholarshipDetails = false;
                  showDocumentUploader = true;
                "
              >
                Upload documents now
              </Button>
            </AlertDescription>
          </Alert>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="showScholarshipDetails = false">
            Cancel
          </Button>
          <Button
            @click="applyForScholarship()"
            :disabled="!canSubmit || scholarshipApplyForm.processing"
          >
            <Icon
              v-if="scholarshipApplyForm.processing"
              icon="lucide:loader-2"
              class="mr-2 h-4 w-4 animate-spin"
            />
            <Icon v-else icon="lucide:send" class="mr-2 h-4 w-4" />
            Apply Now
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Document Uploader Sheet (Copied from previous template, ensure props match) -->
    <Sheet v-model:open="showDocumentUploader">
      <SheetContent class="w-full sm:max-w-md p-0 flex flex-col">
        <SheetHeader class="p-6 border-b">
          <SheetTitle class="flex items-center text-lg">
            <Icon icon="lucide:folder-up" class="mr-2 h-5 w-5 text-primary" />
            Manage Documents
          </SheetTitle>
          <SheetDescription>
            Upload and manage required and optional documents for your
            application.
          </SheetDescription>
        </SheetHeader>

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
          <!-- Progress Summary -->
          <div class="mb-6 p-4 rounded-lg border bg-muted/50">
            <div class="flex items-center justify-between mb-1">
              <h3 class="text-sm font-medium">Required Documents Progress</h3>
              <Badge variant="outline"
                >{{ uploadedRequiredCount }}/{{ requiredDocumentsCount }}</Badge
              >
            </div>
            <Progress
              :model-value="progressPercentage"
              :class="progressColor"
              class="h-2 mb-1"
            />
            <p class="text-xs text-muted-foreground">
              {{ progressPercentage }}% complete -
              {{
                progressPercentage < 100
                  ? `Upload ${requiredDocumentsCount - uploadedRequiredCount} more required document(s)`
                  : "All required documents uploaded!"
              }}
            </p>
          </div>

          <!-- Required Documents Accordion -->
          <div class="space-y-1">
            <h3 class="text-sm font-semibold text-foreground mb-2">
              Required Documents
            </h3>
            <Accordion type="single" collapsible class="w-full space-y-2">
              <AccordionItem
                v-for="docType in documentTypes.filter((d) => d.required)"
                :key="docType.type"
                :value="docType.type"
                class="border rounded-md overflow-hidden"
              >
                <AccordionTrigger
                  class="px-4 py-3 hover:bg-muted/50 text-sm font-medium [&[data-state=open]>div>svg]:rotate-180"
                >
                  <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-2">
                      <Icon
                        :icon="
                          hasDocument(docType.type)
                            ? 'lucide:check-circle-2'
                            : 'lucide:alert-circle'
                        "
                        :class="
                          hasDocument(docType.type)
                            ? 'text-success'
                            : 'text-warning'
                        "
                        class="h-4 w-4 flex-shrink-0"
                      />
                      <span>{{ docType.label }}</span>
                    </div>
                    <Icon
                      icon="lucide:chevron-down"
                      class="h-4 w-4 shrink-0 transition-transform duration-200"
                    />
                  </div>
                </AccordionTrigger>
                <AccordionContent class="px-4 pt-0 pb-4 bg-muted/30">
                  <p
                    class="text-xs text-muted-foreground mb-3 pt-2 border-t border-dashed"
                  >
                    {{ docType.description }}
                  </p>

                  <div
                    v-if="hasDocument(docType.type)"
                    class="mb-3 text-xs p-2 bg-green-50 border border-green-200 rounded-md"
                  >
                    <div
                      class="flex items-center justify-between font-medium text-green-800"
                    >
                      <span>File Uploaded</span>
                      <Badge
                        size="sm"
                        :variant="
                          documents.find((d) => d.type === docType.type)
                            ?.status === 'approved'
                            ? 'success'
                            : 'secondary'
                        "
                      >
                        {{
                          documents.find((d) => d.type === docType.type)
                            ?.status || "Pending"
                        }}
                      </Badge>
                    </div>
                    <p class="text-green-700 mt-0.5">
                      On:
                      {{
                        formatDate(
                          documents.find((d) => d.type === docType.type)
                            ?.created_at,
                        )
                      }}
                    </p>
                  </div>

                  <Button
                    variant="outline"
                    size="sm"
                    @click="openFileUpload(docType.type)"
                    :disabled="
                      uploadingFile && currentDocumentType === docType.type
                    "
                  >
                    <Icon
                      v-if="
                        uploadingFile && currentDocumentType === docType.type
                      "
                      icon="lucide:loader-2"
                      class="mr-1.5 h-4 w-4 animate-spin"
                    />
                    <Icon
                      v-else-if="hasDocument(docType.type)"
                      icon="lucide:replace"
                      class="mr-1.5 h-4 w-4"
                    />
                    <Icon
                      v-else
                      icon="lucide:upload-cloud"
                      class="mr-1.5 h-4 w-4"
                    />
                    {{
                      hasDocument(docType.type) ? "Replace File" : "Upload File"
                    }}
                  </Button>
                </AccordionContent>
              </AccordionItem>
            </Accordion>
          </div>

          <!-- Optional Documents Accordion -->
          <div class="space-y-1">
            <h3 class="text-sm font-semibold text-foreground mb-2">
              Optional Documents
            </h3>
            <Accordion type="single" collapsible class="w-full space-y-2">
              <AccordionItem
                v-for="docType in documentTypes.filter((d) => !d.required)"
                :key="docType.type"
                :value="docType.type"
                class="border rounded-md overflow-hidden"
              >
                <AccordionTrigger
                  class="px-4 py-3 hover:bg-muted/50 text-sm font-medium [&[data-state=open]>div>svg]:rotate-180"
                >
                  <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-2">
                      <Icon
                        :icon="
                          hasDocument(docType.type)
                            ? 'lucide:check-circle'
                            : 'lucide:info'
                        "
                        :class="
                          hasDocument(docType.type)
                            ? 'text-success'
                            : 'text-muted-foreground'
                        "
                        class="h-4 w-4 flex-shrink-0"
                      />
                      <span>{{ docType.label }}</span>
                      <Badge
                        v-if="hasDocument(docType.type)"
                        variant="success"
                        size="sm"
                        >Uploaded</Badge
                      >
                    </div>
                    <Icon
                      icon="lucide:chevron-down"
                      class="h-4 w-4 shrink-0 transition-transform duration-200"
                    />
                  </div>
                </AccordionTrigger>
                <AccordionContent class="px-4 pt-0 pb-4 bg-muted/30">
                  <p
                    class="text-xs text-muted-foreground mb-3 pt-2 border-t border-dashed"
                  >
                    {{ docType.description }}
                  </p>
                  <!-- Display info about uploaded optional doc -->
                  <div
                    v-if="hasDocument(docType.type)"
                    class="mb-3 text-xs p-2 bg-blue-50 border border-blue-200 rounded-md"
                  >
                    <div
                      class="flex items-center justify-between font-medium text-blue-800"
                    >
                      <span>File Uploaded</span>
                      <Badge
                        size="sm"
                        :variant="
                          documents.find((d) => d.type === docType.type)
                            ?.status === 'approved'
                            ? 'success'
                            : 'secondary'
                        "
                      >
                        {{
                          documents.find((d) => d.type === docType.type)
                            ?.status || "Pending"
                        }}
                      </Badge>
                    </div>
                    <p class="text-blue-700 mt-0.5">
                      On:
                      {{
                        formatDate(
                          documents.find((d) => d.type === docType.type)
                            ?.created_at,
                        )
                      }}
                    </p>
                  </div>
                  <Button
                    variant="outline"
                    size="sm"
                    @click="openFileUpload(docType.type)"
                    :disabled="
                      uploadingFile && currentDocumentType === docType.type
                    "
                  >
                    <Icon
                      v-if="
                        uploadingFile && currentDocumentType === docType.type
                      "
                      icon="lucide:loader-2"
                      class="mr-1.5 h-4 w-4 animate-spin"
                    />
                    <Icon
                      v-else-if="hasDocument(docType.type)"
                      icon="lucide:replace"
                      class="mr-1.5 h-4 w-4"
                    />
                    <Icon
                      v-else
                      icon="lucide:upload-cloud"
                      class="mr-1.5 h-4 w-4"
                    />
                    {{
                      hasDocument(docType.type) ? "Replace File" : "Upload File"
                    }}
                  </Button>
                </AccordionContent>
              </AccordionItem>
            </Accordion>
          </div>

          <Alert
            v-if="canSubmit && props.application?.status === 'incomplete'"
            variant="success"
            class="mt-6"
          >
            <Icon icon="lucide:party-popper" class="h-4 w-4" />
            <AlertTitle>Ready to Submit!</AlertTitle>
            <AlertDescription>
              All required documents are uploaded. You can now submit your
              application.
            </AlertDescription>
          </Alert>
        </div>

        <div class="p-6 border-t mt-auto flex gap-2 justify-end bg-background">
          <Button variant="outline" @click="showDocumentUploader = false">
            Close
          </Button>
          <Button
            v-if="canSubmit && props.application?.status === 'incomplete'"
            @click="submitApplication"
            :disabled="appSubmitForm.processing"
          >
            <Icon
              v-if="appSubmitForm.processing"
              icon="lucide:loader-2"
              class="mr-2 h-4 w-4 animate-spin"
            />
            <Icon v-else icon="lucide:send" class="mr-2 h-4 w-4" />
            Submit Application
          </Button>
        </div>

        <!-- Hidden form for triggering upload -->
        <form ref="uploadFormRef" class="hidden">
          <input
            ref="fileInputRef"
            type="file"
            @change="handleFileChange"
            accept=".pdf,.jpg,.jpeg,.png"
          />
        </form>
      </SheetContent>
    </Sheet>

    <!-- Help/Tour Dialog -->
    <Dialog v-model:open="showTour">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle class="flex items-center">
            <Icon icon="lucide:rocket" class="mr-2 h-5 w-5 text-primary" />
            Dashboard Tour
          </DialogTitle>
          <DialogDescription>
            A quick guide to your scholarship portal features.
          </DialogDescription>
        </DialogHeader>
        <div class="py-4 space-y-4 max-h-[60vh] overflow-y-auto pr-2">
          <div class="flex items-start gap-3 p-3 rounded-lg bg-muted/50">
            <Icon
              icon="lucide:clipboard-list"
              class="h-5 w-5 text-primary flex-shrink-0 mt-1"
            />
            <div>
              <h3 class="font-medium text-sm">Application Status</h3>
              <p class="text-xs text-muted-foreground">
                Track your main application progress here. Actions may appear
                based on the current status.
              </p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-3 rounded-lg bg-muted/50">
            <Icon
              icon="lucide:award"
              class="h-5 w-5 text-primary flex-shrink-0 mt-1"
            />
            <div>
              <h3 class="font-medium text-sm">Available Scholarships</h3>
              <p class="text-xs text-muted-foreground">
                Discover new scholarship opportunities you might be eligible
                for. Complete your profile to apply!
              </p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-3 rounded-lg bg-muted/50">
            <Icon
              icon="lucide:file-text"
              class="h-5 w-5 text-primary flex-shrink-0 mt-1"
            />
            <div>
              <h3 class="font-medium text-sm">Document Status</h3>
              <p class="text-xs text-muted-foreground">
                See your document upload progress at a glance. Manage your
                uploads easily.
              </p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-3 rounded-lg bg-muted/50">
            <Icon
              icon="lucide:user-circle"
              class="h-5 w-5 text-primary flex-shrink-0 mt-1"
            />
            <div>
              <h3 class="font-medium text-sm">Personal Info</h3>
              <p class="text-xs text-muted-foreground">
                Review your profile details. Keep them up-to-date via the
                profile settings.
              </p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-3 rounded-lg bg-muted/50">
            <Icon
              icon="lucide:graduation-cap"
              class="h-5 w-5 text-primary flex-shrink-0 mt-1"
            />
            <div>
              <h3 class="font-medium text-sm">Active Scholarships</h3>
              <p class="text-xs text-muted-foreground">
                If approved, your awarded scholarships and their status will
                appear here.
              </p>
            </div>
          </div>
        </div>
        <DialogFooter>
          <Button @click="showTour = false" class="w-full">
            <Icon icon="lucide:check-check" class="mr-2 h-4 w-4" />
            Got it!
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
