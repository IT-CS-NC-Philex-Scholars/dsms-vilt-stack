<script setup>
import { ref, computed, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter,
} from "@/Components/shadcn/ui/card";
import Button from "@/Components/shadcn/ui/button/Button.vue";
import Badge from "@/Components/shadcn/ui/badge/Badge.vue";
import Progress from "@/Components/shadcn/ui/progress/Progress.vue";
import Alert from "@/Components/shadcn/ui/alert/Alert.vue";
import AlertTitle from "@/Components/shadcn/ui/alert/AlertTitle.vue";
import AlertDescription from "@/Components/shadcn/ui/alert/AlertDescription.vue";
import { Separator } from "@/Components/shadcn/ui/separator";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogFooter,
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
import { toast } from "vue-sonner";
import Sonner from "@/Components/shadcn/ui/sonner/Sonner.vue";
import Avatar from "@/Components/shadcn/ui/avatar/Avatar.vue";
import AvatarImage from "@/Components/shadcn/ui/avatar/AvatarImage.vue";
import AvatarFallback from "@/Components/shadcn/ui/avatar/AvatarFallback.vue";

const props = defineProps({
  application: Object,
  documents: Array,
  scholar: Object,
  documentTypes: Array,
  availableScholarships: Array,
  activeScholarships: Array,
});

// UI state
const fileInput = ref(null);
const uploadForm = ref(null);
const currentDocumentType = ref(null);
const uploadingFile = ref(false);
const selectedScholarship = ref(null);
const showScholarshipDetails = ref(false);
const showDocumentUploader = ref(false);
const showTour = ref(false);

// Filter scholarships that the scholar can apply for
const eligibleScholarships = computed(() => {
  return (
    props.availableScholarships?.filter(
      (scholarship) =>
        !props.activeScholarships?.some((active) => active.id === scholarship.id)
    ) || []
  );
});

const requiredDocumentsCount = computed(() => {
  return props.documentTypes?.filter((doc) => doc.required).length || 0;
});

const uploadedRequiredCount = computed(() => {
  const requiredTypes = props.documentTypes
    ?.filter((doc) => doc.required)
    .map((doc) => doc.type) || [];

  return props.documents?.filter((doc) =>
    requiredTypes.includes(doc.type)
  ).length || 0;
});

const progressPercentage = computed(() => {
  const requiredDocs = requiredDocumentsCount.value;
  return requiredDocs > 0
    ? Math.min(100, Math.round((uploadedRequiredCount.value / requiredDocs) * 100))
    : 0;
});

const progressColor = computed(() => {
  if (progressPercentage.value < 40) return "bg-destructive";
  if (progressPercentage.value < 75) return "bg-warning";
  return "bg-success";
});

const applicationStatus = computed(() => {
  switch (props.application?.status) {
    case "incomplete":
      return { label: "Incomplete", variant: "warning", icon: "lucide:alert-triangle" };
    case "submitted":
      return { label: "Under Review", variant: "info", icon: "lucide:hourglass" };
    case "approved":
      return { label: "Approved", variant: "success", icon: "lucide:check-circle" };
    case "rejected":
      return { label: "Rejected", variant: "destructive", icon: "lucide:x-circle" };
    default:
      return { label: "Pending", variant: "secondary", icon: "lucide:clock" };
  }
});

const canSubmit = computed(() => {
  return uploadedRequiredCount.value >= requiredDocumentsCount.value;
});

const userInitials = computed(() => {
  const firstName = props.scholar?.first_name || '';
  const lastName = props.scholar?.last_name || '';
  return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
});

// Functions
function selectScholarship(scholarship) {
  selectedScholarship.value = scholarship;
  showScholarshipDetails.value = true;
}

function hasDocument(type) {
  return props.documents?.some((doc) => doc.type === type) || false;
}

function openFileUpload(documentType) {
  currentDocumentType.value = documentType;
  fileInput.value.click();
}

function handleFileChange() {
  if (fileInput.value.files.length > 0) {
    uploadDocument();
  }
}

function uploadDocument() {
  if (!fileInput.value.files.length) {
    return;
  }

  const formData = new FormData();
  formData.append("file", fileInput.value.files[0]);
  formData.append("document_type", currentDocumentType.value);

  uploadingFile.value = true;

  useForm(formData).post(route("scholar.upload-document"), {
    forceFormData: true,
    onSuccess: () => {
      toast.success("Document uploaded successfully");
      fileInput.value.value = "";
      showDocumentUploader.value = false;
    },
    onError: (errors) => {
      const errorMessage = Object.values(errors)[0] || "Error uploading document";
      toast.error(errorMessage);
    },
    onFinish: () => {
      uploadingFile.value = false;
    },
  });
}

function applyForScholarship() {
  if (!selectedScholarship.value) return;

  useForm({
    scholarship_id: selectedScholarship.value.id,
  }).post(route("scholar.apply-scholarship"), {
    onSuccess: () => {
      toast.success(
        `You've successfully applied for the ${selectedScholarship.value.name} scholarship`,
        {
          description: "Your application will be reviewed by our team."
        }
      );
      showScholarshipDetails.value = false;
      selectedScholarship.value = null;
    },
    onError: (errors) => {
      const errorMessage = Object.values(errors)[0] || "Error applying for scholarship";
      toast.error(errorMessage);
    },
  });
}

function submitApplication() {
  if (!canSubmit.value) {
    toast.error("Please upload all required documents first");
    return;
  }

  useForm().post(route("scholar.submit-application"), {
    onSuccess: () => {
      toast.success("Application submitted successfully", {
        description: "Your application is now under review."
      });
    },
    onError: (errors) => {
      const errorMessage = Object.values(errors)[0] || "Error submitting application";
      toast.error(errorMessage);
    },
  });
}

function formatDate(dateString) {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

function getDaysRemaining(dateString) {
  if (!dateString) return 0;
  const deadline = new Date(dateString);
  const today = new Date();
  const diffTime = deadline - today;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays > 0 ? diffDays : 0;
}

function startTour() {
  showTour.value = true;
  // Implement tour logic here
}

onMounted(() => {
  // Check if this is the user's first visit to show a tour
  const isFirstVisit = !localStorage.getItem('dashboardVisited');
  if (isFirstVisit) {
    localStorage.setItem('dashboardVisited', 'true');
    setTimeout(() => {
      startTour();
    }, 1000);
  }
});
</script>

<template>
  <AppLayout title="Scholar Dashboard">
    <Sonner position="top-center" />

    <div class="bg-accent/10 min-h-screen pb-12">
      <!-- Hero section with profile summary -->
      <div class="bg-gradient-to-r from-primary/10 to-primary/5 pt-8 pb-16 px-4 relative">
        <div class="container max-w-6xl mx-auto">
          <div class="flex flex-col md:flex-row gap-6 items-start md:items-center">
            <!-- Avatar and name -->
            <div class="flex items-center gap-4">
              <Avatar class="h-16 w-16 border-2 border-primary/20">
                <AvatarImage :src="scholar?.avatar_url" alt="Profile" />
                <AvatarFallback class="text-lg bg-primary/10">{{ userInitials }}</AvatarFallback>
              </Avatar>
              <div>
                <h1 class="text-2xl font-bold">
                  Welcome, {{ scholar?.first_name }}!
                </h1>
                <div class="flex items-center mt-1">
                  <Badge :variant="applicationStatus.variant" class="mr-2">
                    <Icon :icon="applicationStatus.icon" class="h-3.5 w-3.5 mr-1" />
                    {{ applicationStatus.label }}
                  </Badge>
                  <span v-if="progressPercentage < 100" class="text-sm text-muted-foreground">
                    Complete your profile to apply for scholarships
                  </span>
                </div>
              </div>
            </div>

            <!-- Action buttons for mobile -->
            <div class="flex md:hidden w-full gap-2 mt-4">
              <Button variant="default" class="flex-1" @click="showDocumentUploader = true">
                <Icon icon="lucide:file-plus" class="mr-2 h-4 w-4" />
                Upload Documents
              </Button>
              <Button variant="outline" class="flex-1" @click="startTour">
                <Icon icon="lucide:help-circle" class="mr-2 h-4 w-4" />
                Help
              </Button>
            </div>

            <!-- Progress bar for large screens -->
            <div class="hidden md:flex flex-1 items-center gap-4 ml-auto">
              <div class="flex-1 max-w-xs">
                <p class="text-sm font-medium mb-1.5 flex items-center justify-between">
                  <span>Application Progress</span>
                  <span>{{ progressPercentage }}%</span>
                </p>
                <Progress :value="progressPercentage" :class="progressColor" class="h-2" />
              </div>
              <Button variant="default" @click="showDocumentUploader = true">
                <Icon icon="lucide:file-plus" class="mr-2 h-4 w-4" />
                Upload Documents
              </Button>
              <Button variant="outline" @click="startTour">
                <Icon icon="lucide:help-circle" class="mr-2 h-4 w-4" />
                Help
              </Button>
            </div>
          </div>
        </div>
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
