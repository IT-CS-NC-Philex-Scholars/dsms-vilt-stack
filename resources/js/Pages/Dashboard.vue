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
import { Label } from "@/Components/shadcn/ui/label";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/Components/shadcn/ui/select";

import { Icon } from "@iconify/vue";
import { usePage, Link, useForm } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue"; // Import ref and watch
import { toast } from "vue-sonner";
import axios from "axios"; // Import axios for document uploads

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
  currentAcademicYear: Number, // Added for academic year
});

// --- Ref for Dialogs/Sheets ---
const showScholarshipDetails = ref(false);
const selectedScholarship = ref(null);
const showDocumentUploader = ref(false);
const showTour = ref(false); // Optional tour state
const showSemesterSelector = ref(false);

// --- Upload Logic Refs ---
const uploadFormRef = ref(null);
const fileInputRef = ref(null);
const currentDocumentType = ref(null);
const uploadingFile = ref(false);
const currentDocTypeMeta = ref(null);
const selectedSemester = ref(null);

// --- Forms ---
const docUploadForm = useForm({
  document_type: null,
  file: null,
  semester_type: null,
  semester_number: null,
  academic_year: null,
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

const needsToCompleteApplication = computed(() => {
  return props.application &&
         (props.application.status === 'draft' || props.application.status === 'incomplete') &&
         progressPercentage.value < 100;
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
  
  // Count unique document types that are required
  const uploadedTypes = new Set();
  props.documents.forEach(doc => {
    if (requiredTypes.includes(doc.type)) {
      uploadedTypes.add(doc.type);
    }
  });
  
  return uploadedTypes.size;
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

// *** ADDED: Get semester info based on Scholar model ***
const scholarSemesterInfo = computed(() => {
  if (!props.scholar) return null;
  
  const semesterType = props.scholar.additional_details?.semester_system || null;
  const yearLevel = props.scholar.year_level || null;
  
  return {
    type: semesterType,
    yearLevel: yearLevel,
  };
});

// *** ADDED: Compute max semesters based on semester type ***
const maxSemestersPerYear = computed(() => {
  if (!scholarSemesterInfo.value || !scholarSemesterInfo.value.type) return 2;
  return scholarSemesterInfo.value.type === 'semestral' ? 2 : 3;
});

// *** ADDED: Generate semester options for select dropdown ***
const semesterOptions = computed(() => {
  const options = [];
  const currentYear = props.currentAcademicYear || new Date().getFullYear();
  
  // Add current and previous year as options
  for (let year = currentYear; year >= currentYear - 1; year--) {
    const yearLabel = `${year}-${year + 1} Academic Year`;
    
    for (let sem = 1; sem <= maxSemestersPerYear.value; sem++) {
      const semLabel = scholarSemesterInfo.value?.type === 'trimesteral'
        ? (sem === 1 ? '1st Trimester' : (sem === 2 ? '2nd Trimester' : '3rd Trimester'))
        : (sem === 1 ? '1st Semester' : '2nd Semester');
      
      options.push({
        label: `${semLabel} (${yearLabel})`,
        value: {
          semester_type: scholarSemesterInfo.value?.type || 'semestral',
          semester_number: sem,
          academic_year: year,
        }
      });
    }
  }
  
  return options;
});

// *** ADDED: Group semester options by academic year for better UX ***
const semesterOptionsByYear = computed(() => {
  const grouped = {};
  
  semesterOptions.value.forEach(option => {
    const yearLabel = `${option.value.academic_year}-${option.value.academic_year + 1}`;
    
    if (!grouped[yearLabel]) {
      grouped[yearLabel] = {
        year: yearLabel,
        options: []
      };
    }
    
    grouped[yearLabel].options.push(option);
  });
  
  // Convert to array and sort by most recent year first
  return Object.values(grouped).sort((a, b) => {
    const yearA = parseInt(a.year.split('-')[0]);
    const yearB = parseInt(b.year.split('-')[0]);
    return yearB - yearA;
  });
});

// Open file input for specific document type
const openFileUpload = (docType) => {
  // Find the document type metadata
  currentDocTypeMeta.value = props.documentTypes.find(dt => dt.type === docType);
  currentDocumentType.value = docType;
  
  docUploadForm.reset(); // Clear previous file if any
  docUploadForm.document_type = docType;
  
  // If document requires semester information and scholar has a semester type
  if (currentDocTypeMeta.value?.requires_semester && scholarSemesterInfo.value?.type) {
    // Set default semester to latest one
    const latestSemester = semesterOptions.value[0]?.value;
    if (latestSemester) {
      docUploadForm.semester_type = latestSemester.semester_type;
      docUploadForm.semester_number = latestSemester.semester_number;
      docUploadForm.academic_year = latestSemester.academic_year;
    }
  } else {
    // Reset semester fields for documents that don't need it
    docUploadForm.semester_type = null;
    docUploadForm.semester_number = null;
    docUploadForm.academic_year = null;
  }
  
  // Use timeout to ensure the file input is reset before clicking
  setTimeout(() => {
    fileInputRef.value?.click();
  }, 100);
};

// *** MODIFIED: Handle file selection with semester check ***
const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (!file || !currentDocumentType.value) {
    return;
  }
  
  docUploadForm.file = file;
  
  // Check if semester selection is required for this document type
  if (currentDocTypeMeta.value?.requires_semester && scholarSemesterInfo.value?.type) {
    // Show semester selector dialog
    selectedSemester.value = null; // Reset selection
    showSemesterSelector.value = true;
  } else {
    // No semester info needed, proceed with upload
    uploadDocument();
  }
  
  // Reset file input visually
  if (event.target) event.target.value = null;
};

// *** ADDED: Function to handle semester selection confirmation ***
const confirmSemesterSelection = () => {
  if (!selectedSemester.value) {
    toast.error("Please select a semester/trimester.");
    return;
  }
  
  // Apply selected semester to the upload form
  docUploadForm.semester_type = selectedSemester.value.semester_type;
  docUploadForm.semester_number = selectedSemester.value.semester_number;
  docUploadForm.academic_year = selectedSemester.value.academic_year;
  
  // Close dialog and proceed with upload
  showSemesterSelector.value = false;
  uploadDocument();
};

// Upload the document
const uploadDocument = () => {
  if (!docUploadForm.file || !docUploadForm.document_type) {
    toast.error("File or document type missing.");
    return;
  }
  
  // Validate semester information if needed
  if (currentDocTypeMeta.value?.requires_semester && scholarSemesterInfo.value?.type && 
      (!docUploadForm.semester_type || !docUploadForm.semester_number || !docUploadForm.academic_year)) {
    toast.error("Please select a semester for this document.");
    return;
  }
  
  uploadingFile.value = true;
  
  // Use Inertia's form submission to maintain SPA behavior
  docUploadForm.post(route("dashboard.upload-document"), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      let successMsg = `${currentDocumentType.value.replace(/_/g, " ")} uploaded successfully!`;
      
      // Add semester info to success message if relevant
      if (docUploadForm.semester_type && docUploadForm.semester_number) {
        const semText = docUploadForm.semester_type === 'semestral' ? 'Semester' : 'Trimester';
        const semNum = docUploadForm.semester_number === 1 ? '1st' : 
                     (docUploadForm.semester_number === 2 ? '2nd' : '3rd');
        successMsg += ` (${semNum} ${semText} ${docUploadForm.academic_year}-${docUploadForm.academic_year + 1})`;
      }
      
      toast.success(successMsg);
      docUploadForm.reset();
      currentDocumentType.value = null;
      currentDocTypeMeta.value = null;
    },
    onError: (errors) => {
      console.error("Upload Error:", errors);
      const errorMsg =
        errors.file ||
        errors.document_type ||
        errors.semester_type ||
        errors.semester_number ||
        errors.academic_year ||
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
  // Must have an approved application and all required docs must be uploaded
  return props.application && 
         props.application.status === 'approved' && 
         progressPercentage.value === 100;
});

// Helper to get specific document from props.documents by type and semester
const getDocumentByTypeAndSemester = (docType, semType, semNumber, acadYear) => {
  if (!props.documents) return null;
  
  // If semester info is provided, filter by it
  if (semType && semNumber && acadYear) {
    return props.documents.find(doc => 
      doc.type === docType &&
      doc.semester_type === semType &&
      doc.semester_number === semNumber &&
      doc.academic_year === acadYear
    );
  }
  
  // Otherwise just filter by type
  return props.documents.find(doc => doc.type === docType);
};

// Get semester-specific documents formatted for display
const getSemesterDocuments = (docType) => {
  if (!props.documents) return [];
  
  // Filter documents by type and group them by semester
  return props.documents.filter(doc => 
    doc.type === docType && 
    doc.semester_type && 
    doc.semester_number && 
    doc.academic_year
  ).sort((a, b) => {
    // Sort by academic year (most recent first) and then by semester number
    if (a.academic_year !== b.academic_year) return b.academic_year - a.academic_year;
    return b.semester_number - a.semester_number;
  });
};

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

// Check if there are semester-specific documents
const hasSemesterDocuments = computed(() => {
  return props.documents?.some(doc => doc.semester_type && doc.semester_number && doc.academic_year) || false;
});

// Group semester documents by academic year and semester
const semesterDocumentsGrouped = computed(() => {
  if (!props.documents) return [];
  
  const semesterDocs = props.documents.filter(d => 
    d.semester_type && d.semester_number && d.academic_year
  );
  
  // Group by academic year
  const byYear = {};
  semesterDocs.forEach(doc => {
    const yearKey = `${doc.academic_year}-${doc.academic_year + 1}`;
    if (!byYear[yearKey]) {
      byYear[yearKey] = {
        year: yearKey,
        semesters: {}
      };
    }
    
    // Group by semester within year
    const semKey = `${doc.semester_type}_${doc.semester_number}`;
    if (!byYear[yearKey].semesters[semKey]) {
      byYear[yearKey].semesters[semKey] = {
        type: doc.semester_type,
        number: doc.semester_number,
        documents: []
      };
    }
    
    byYear[yearKey].semesters[semKey].documents.push(doc);
  });
  
  // Convert to arrays for easier template iteration
  const result = Object.values(byYear).map(year => {
    year.semesters = Object.values(year.semesters).sort((a, b) => a.number - b.number);
    return year;
  }).sort((a, b) => {
    // Sort by most recent academic year
    const yearA = parseInt(a.year.split('-')[0]);
    const yearB = parseInt(b.year.split('-')[0]);
    return yearB - yearA;
  });
  
  return result;
});

// Get document type label from type code
const getDocumentTypeLabel = (type) => {
  const docType = props.documentTypes?.find(d => d.type === type);
  return docType?.label || type.replace(/_/g, ' ');
};

// Get semester label from type and number
const getSemesterLabel = (type, number) => {
  if (type === 'semestral') {
    return number === 1 ? '1st Semester' : '2nd Semester';
  } else {
    return number === 1 ? '1st Trimester' : 
           number === 2 ? '2nd Trimester' : '3rd Trimester';
  }
};

// Check if application is under review
const isApplicationUnderReview = computed(() => {
  const status = props.application?.status?.toLowerCase();
  return status === 'submitted' || status === 'pending' || status === 'pending_review';
});

const getDocumentStatusInfo = (docType, isOptional = false) => {
  const doc = getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year);
  if (!doc) return { text: 'Pending', variant: 'secondary' };
  return {
    text: doc.status ? doc.status.replace('_', ' ') : 'Pending',
    variant: getStatusVariant(doc.status)
  };
};

const getIconForDocType = (docType, isOptional = false) => {
  const doc = getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year);
  if (!doc) return 'lucide:file-plus-2';
  return doc.status === 'approved' ? 'lucide:check-circle' : (doc.status === 'rejected' ? 'lucide:x-circle' : 'lucide:file-clock');
};

const getColorForDocTypeStatus = (docType, isOptional = false) => {
  const doc = getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year);
  if (!doc) return 'text-muted-foreground';
  return doc.status === 'approved' ? 'text-success' : (doc.status === 'rejected' ? 'text-destructive' : 'text-warning');
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
        <!-- Prominent Alert for Incomplete Application -->
        <Alert
          v-if="needsToCompleteApplication"
          variant="warning"
          class="mb-6 lg:mb-8 mx-4 sm:mx-0"
        >
          <Icon icon="lucide:alert-triangle" class="h-5 w-5" />
          <AlertTitle class="font-semibold">Action Required: Complete Your Application</AlertTitle>
          <AlertDescription class="mt-1">
            Your scholarship application is not yet complete. Please upload all required documents to proceed.
            <Button
              variant="link"
              class="p-0 h-auto font-semibold text-yellow-700 dark:text-yellow-300 hover:underline ml-1 inline-block align-baseline"
              @click="showDocumentUploader = true"
            >
              Upload Documents Now
            </Button>
          </AlertDescription>
        </Alert>

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

        <!-- Application Under Review Notification - Show when application is under review -->
        <Card 
          v-if="isApplicationUnderReview"
          class="shadow-md border-l-4 border-l-info mx-4 sm:mx-0"
        >
          <CardContent class="p-6">
            <div class="flex flex-col md:flex-row items-center gap-4">
              <div class="bg-info/20 p-4 rounded-full">
                <Icon icon="lucide:hourglass" class="h-12 w-12 text-info" />
              </div>
              <div class="text-center md:text-left">
                <h2 class="text-xl font-semibold mb-2">Application Under Review</h2>
                <p class="text-muted-foreground">
                  Your application is currently being evaluated by our team. During this review period, some dashboard features are limited.
                </p>
                <p class="mt-3 text-sm font-medium">
                  We'll notify you of any updates. You can still view your profile details and submitted documents below.
                </p>
                <p class="mt-2 text-xs text-muted-foreground">
                  Application submitted on: {{ formatDate(props.application?.updated_at) }}
                </p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Main Content Area - Show full content when not under review, simplified when under review -->
        <div class="px-4 sm:px-0">
          <!-- Top Row: Key Action Cards -->
          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6 lg:mb-8">
            <!-- Application status card (always shown) -->
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

            <!-- Scholarships card - show in limited mode if under review -->
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
                      :icon="
                        isApplicationUnderReview || props.application?.status !== 'approved'
                          ? 'lucide:lock'
                          : 'lucide:search-check'
                      "
                      :class="
                        isApplicationUnderReview || props.application?.status !== 'approved'
                          ? 'text-muted-foreground'
                          : 'text-primary'
                      "
                      class="h-5 w-5"
                    />
                  </div>
                  <div>
                    <h3 class="font-semibold text-lg text-foreground">
                      {{ (isApplicationUnderReview || props.application?.status !== 'approved') ? '—' : eligibleScholarships.length }}
                    </h3>
                    <p class="text-xs text-muted-foreground leading-tight">
                      {{ isApplicationUnderReview 
                          ? 'Available after review' 
                          : (props.application?.status !== 'approved'
                            ? 'Available after approval'
                            : (eligibleScholarships.length > 0
                              ? "Scholarships matching your profile"
                              : "No new opportunities now")) }}
                    </p>
                  </div>
                </div>
                <div v-if="!isApplicationUnderReview && props.application?.status === 'approved'" class="mb-3 flex items-center gap-2 text-xs">
                  <Badge variant="outline"
                    >{{ eligibleScholarships.length }} Available</Badge
                  >
                  <Badge variant="outline"
                    >{{ activeScholarships?.length || 0 }} Applied</Badge
                  >
                </div>
                <div v-else class="p-2 bg-muted rounded-md mb-3">
                  <p class="text-xs text-center text-muted-foreground">
                    {{ isApplicationUnderReview 
                      ? "Scholarship options will be available after your application review is complete."
                      : "Scholarship options will be available after your application is approved." }}
                  </p>
                </div>

                <Button
                  variant="outline"
                  class="w-full"
                  :disabled="isApplicationUnderReview || props.application?.status !== 'approved' || (!canSubmit && eligibleScholarships.length > 0)"
                  @click="
                    !canSubmit && eligibleScholarships.length > 0
                      ? (showDocumentUploader = true)
                      : null /* TODO: Link to scholarship browse page */
                  "
                  size="sm"
                >
                  <Icon
                    :icon="
                      isApplicationUnderReview || props.application?.status !== 'approved'
                        ? 'lucide:lock'
                        : (!canSubmit && eligibleScholarships.length > 0
                        ? 'lucide:lock'
                          : 'lucide:search')
                    "
                    class="mr-2 h-4 w-4"
                  />
                  {{ isApplicationUnderReview 
                      ? "Available After Review" 
                      : (props.application?.status !== 'approved'
                      ? "Available After Approval"
                      : (!canSubmit && eligibleScholarships.length > 0
                        ? "Complete Profile to Apply"
                        : eligibleScholarships.length > 0
                          ? "Browse Scholarships"
                              : "Check Back Later")) 
                  }}
                </Button>
              </CardContent>
            </Card>

            <!-- Documents card - show in limited mode if under review -->
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
                        isApplicationUnderReview
                          ? 'lucide:file-check'
                          : (progressPercentage === 100
                          ? 'lucide:check-check'
                            : 'lucide:file-warning')
                      "
                      class="h-5 w-5"
                      :class="
                        isApplicationUnderReview
                          ? 'text-info'
                          : (progressPercentage === 100
                          ? 'text-success'
                            : 'text-warning')
                      "
                    />
                  </div>
                  <div>
                    <h3 class="font-semibold text-lg text-foreground">
                      {{ isApplicationUnderReview ? 'Submitted' : progressPercentage + '%' }}
                    </h3>
                    <p class="text-xs text-muted-foreground leading-tight">
                      {{ isApplicationUnderReview 
                          ? "Documents under evaluation" 
                          : (progressPercentage < 100
                          ? "Required documents pending"
                            : "All required documents uploaded") 
                      }}
                    </p>
                  </div>
                </div>
                <div v-if="!isApplicationUnderReview" class="mb-3 flex items-center gap-2 text-xs">
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
                <div v-else class="mb-3">
                  <Badge variant="outline" class="w-full justify-center">{{ documents?.length || 0 }} Documents Submitted</Badge>
                </div>

                <Button
                  :variant="isApplicationUnderReview ? 'outline' : (uploadedRequiredCount < requiredDocumentsCount ? 'default' : 'outline')"
                  class="w-full"
                  @click="showDocumentUploader = true"
                  size="sm"
                >
                  <Icon
                    :icon="
                      isApplicationUnderReview
                        ? 'lucide:eye'
                        : (uploadedRequiredCount < requiredDocumentsCount
                        ? 'lucide:file-plus'
                          : 'lucide:folder-cog')
                    "
                    class="mr-2 h-4 w-4"
                  />
                  {{ isApplicationUnderReview 
                      ? "View Submitted Documents" 
                      : (uploadedRequiredCount < requiredDocumentsCount
                      ? "Upload Documents"
                        : "Manage Documents") 
                  }}
                </Button>
              </CardContent>
            </Card>
          </div>

          <!-- Bottom Row: Detailed Info - Hide more detailed content when under review -->
          <div v-if="!isApplicationUnderReview" class="grid lg:grid-cols-3 gap-6 lg:gap-8">
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
                    v-if="props.application?.status !== 'approved'"
                    class="text-center py-8"
                  >
                    <div
                      class="mx-auto w-12 h-12 rounded-full bg-muted flex items-center justify-center mb-3"
                    >
                      <Icon
                        icon="lucide:lock"
                        class="h-6 w-6 text-muted-foreground"
                      />
                    </div>
                    <h3 class="font-medium mb-1 text-sm">
                      Application Pending Approval
                    </h3>
                    <p class="text-xs text-muted-foreground max-w-xs mx-auto">
                      You'll be able to see and apply for scholarships once your main application is approved by our team.
                    </p>
                  </div>
                  <div
                    v-else-if="eligibleScholarships.length === 0"
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
                              getDaysRemaining(scholarship.application_period_end || scholarship.application_deadline)
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
                                formatDate(scholarship.application_period_end || scholarship.application_deadline)
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
                  <div class="space-y-4">
                    <p
                      class="text-xs text-muted-foreground text-center py-4"
                      v-if="!documents || documents.length === 0"
                    >
                      No documents have been uploaded yet.
                    </p>
                    
                    <div v-else>
                      <!-- Regular documents (non-semester) -->
                      <div v-if="documents.filter(d => !d.semester_type).length > 0" class="mb-4">
                        <h4 class="text-sm font-medium text-muted-foreground mb-2 flex items-center">
                          <Icon icon="lucide:file-stack" class="h-4 w-4 mr-1.5" />
                          General Documents
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                          <div
                            v-for="document in documents.filter(d => !d.semester_type || !d.semester_number).slice(0, 4)" 
                            :key="document.id"
                            class="flex items-center justify-between p-3 border rounded-lg text-sm bg-card hover:shadow-md transition-shadow"
                          >
                            <div class="flex items-center gap-2 min-w-0">
                              <Icon
                                :icon="document.status === 'approved' ? 'lucide:check-circle' : (document.status === 'rejected' ? 'lucide:x-circle' : 'lucide:file-clock')"
                                :class="{
                                  'text-success': document.status === 'approved',
                                  'text-destructive': document.status === 'rejected',
                                  'text-warning': document.status === 'pending' || !document.status,
                                  'text-info': document.status === 'submitted' || document.status === 'pending_review'
                                }"
                                class="h-5 w-5 flex-shrink-0"
                              />
                              <span class="font-medium truncate" :title="getDocumentTypeLabel(document.type)">
                                {{ getDocumentTypeLabel(document.type) }}
                              </span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                              <Badge
                                size="sm"
                                :variant="
                                  document.status === 'approved'
                                    ? 'success'
                                    : document.status === 'rejected'
                                      ? 'destructive'
                                      : (document.status === 'pending' || !document.status ? 'warning' : 'info')
                                "
                              >
                                {{ document.status ? document.status.replace('_',' ') : 'Pending' }}
                              </Badge>
                                <!-- Add a view button/modal trigger here if needed -->
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Semester-based documents (grouped) -->
                      <div v-if="hasSemesterDocuments">
                        <h4 class="text-sm font-medium text-muted-foreground mb-2 flex items-center">
                          <Icon icon="lucide:calendar-check-2" class="h-4 w-4 mr-1.5" />
                          Semester-Specific Documents
                        </h4>
                        <div class="space-y-3">
                          <div v-for="(yearGroup, yearIndex) in semesterDocumentsGrouped" :key="yearIndex" 
                              class="p-3 border rounded-lg bg-muted/30 dark:bg-muted/10">
                            <div class="text-xs font-semibold text-muted-foreground mb-2">
                              {{ yearGroup.year }} Academic Year
                            </div>
                            <div class="space-y-2">
                              <div v-for="(semGroup, semIndex) in yearGroup.semesters" :key="semIndex" 
                                  class="border border-dashed dark:border-gray-700 rounded-md p-2.5 bg-card dark:bg-background/50">
                                <div class="flex justify-between items-center text-xs text-muted-foreground mb-1.5">
                                  <span class="font-semibold text-foreground">
                                    {{ getSemesterLabel(semGroup.type, semGroup.number) }}
                                  </span>
                                  <Badge variant="outline" size="sm">{{ semGroup.documents.length }} item(s)</Badge>
                                </div>
                                <div class="space-y-1.5">
                                  <div 
                                    v-for="doc in semGroup.documents" 
                                    :key="doc.id"
                                    class="flex items-center justify-between p-2 border bg-background dark:bg-muted/20 rounded-md text-xs hover:shadow-sm transition-shadow"
                                  >
                                    <div class="flex items-center gap-1.5 min-w-0">
                                      <Icon 
                                        :icon="doc.status === 'approved' ? 'lucide:check-circle' : (doc.status === 'rejected' ? 'lucide:x-circle' : 'lucide:file-clock')" 
                                        :class="{
                                          'text-success': doc.status === 'approved',
                                          'text-destructive': doc.status === 'rejected',
                                          'text-warning': doc.status === 'pending' || !doc.status,
                                          'text-info': doc.status === 'submitted' || doc.status === 'pending_review'
                                        }"
                                        class="h-4 w-4 flex-shrink-0" 
                                      />
                                      <span class="truncate" :title="getDocumentTypeLabel(doc.type)">{{ getDocumentTypeLabel(doc.type) }}</span>
                                    </div>
                                    <Badge 
                                      size="sm"
                                      :variant="doc.status === 'approved' ? 'success' : (doc.status === 'rejected' ? 'destructive' : (doc.status === 'pending' || !doc.status ? 'warning' : 'info'))"
                                    >
                                      {{ doc.status ? doc.status.replace('_',' ') : 'Pending' }}
                                    </Badge>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div v-if="documents.length > 4" class="text-center pt-4">
                        <Button variant="outline" size="sm" @click="showDocumentUploader = true">
                          <Icon icon="lucide:folder-cog" class="h-4 w-4 mr-1.5" />
                          Manage All {{ documents.length }} Documents
                        </Button>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
          
          <!-- Limited information section for application under review -->
          <div v-else class="grid gap-6 lg:gap-8">
            <!-- Personal Information Card -->
            <Card class="shadow-sm">
              <CardHeader>
                <CardTitle class="text-base font-medium flex items-center">
                  <Icon icon="lucide:user-circle" class="h-4 w-4 mr-2 text-muted-foreground" />
                  Personal Information
                </CardTitle>
              </CardHeader>
              <CardContent class="text-sm space-y-2">
                <div v-if="props.scholar">
                  <p>
                    <strong class="text-muted-foreground font-normal w-20 inline-block">Name:</strong>
                    {{ props.scholar.first_name }} {{ props.scholar.last_name }}
                  </p>
                  <p>
                    <strong class="text-muted-foreground font-normal w-20 inline-block">Email:</strong>
                    {{ props.scholar.email }}
                  </p>
                  <p>
                    <strong class="text-muted-foreground font-normal w-20 inline-block">Contact:</strong>
                    {{ props.scholar.contact_number }}
                  </p>
        </div>
                <div v-else class="text-muted-foreground text-xs text-center py-4">
                  Scholar profile not found.
                </div>
              </CardContent>
              <CardFooter class="border-t pt-3">
                <Button :as="Link" :href="route('profile.show')" variant="outline" size="sm">
                  <Icon icon="lucide:user" class="mr-1.5 h-3.5 w-3.5" />
                  View Full Profile
                </Button>
              </CardFooter>
            </Card>
            
            <!-- Document Summary Card -->
            <Card class="shadow-sm" v-if="documents && documents.length > 0">
              <CardHeader>
                <CardTitle class="text-base font-medium flex items-center">
                  <Icon icon="lucide:folder-check" class="h-4 w-4 mr-2 text-muted-foreground" />
                  Submitted Documents Summary
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="p-3 rounded-lg border bg-muted/50 mb-4">
                  <p class="text-sm text-center">
                    Your documents have been submitted and are currently under review.
                  </p>
                </div>
                
                <div class="space-y-2">
                  <div class="flex justify-between items-center text-sm">
                    <span>Total Documents:</span>
                    <Badge>{{ documents.length }}</Badge>
                  </div>
                  <div class="flex justify-between items-center text-sm">
                    <span>Standard Documents:</span>
                    <Badge variant="outline">{{ documents.filter(d => !d.semester_type || !d.semester_number).length }}</Badge>
                  </div>
                  <div class="flex justify-between items-center text-sm">
                    <span>Semester Documents:</span>
                    <Badge variant="outline">{{ documents.filter(d => d.semester_type && d.semester_number).length }}</Badge>
                  </div>
                </div>
                
                <Button variant="outline" class="w-full mt-4" @click="showDocumentUploader = true" size="sm">
                  <Icon icon="lucide:eye" class="mr-2 h-4 w-4" />
                  View Submitted Documents
                </Button>
              </CardContent>
            </Card>
            
            <!-- What's Next Card -->
            <Card class="shadow-sm bg-muted/10">
              <CardHeader>
                <CardTitle class="text-base font-medium flex items-center">
                  <Icon icon="lucide:help-circle" class="h-4 w-4 mr-2 text-muted-foreground" />
                  What's Next?
                </CardTitle>
              </CardHeader>
              <CardContent>
                <ol class="space-y-4 pl-6 list-decimal text-sm">
                  <li class="pl-1">
                    <span class="font-medium">Application Review</span>
                    <p class="text-muted-foreground text-xs mt-1">Our team is currently reviewing your application and documents.</p>
                  </li>
                  <li class="pl-1">
                    <span class="font-medium">Verification Process</span>
                    <p class="text-muted-foreground text-xs mt-1">We may contact you for additional information during this phase.</p>
                  </li>
                  <li class="pl-1">
                    <span class="font-medium">Decision Notification</span>
                    <p class="text-muted-foreground text-xs mt-1">You'll receive an email about the status of your application.</p>
                  </li>
                  <li class="pl-1">
                    <span class="font-medium">Scholarship Matching</span>
                    <p class="text-muted-foreground text-xs mt-1">Once approved, you'll be matched with eligible scholarships.</p>
                  </li>
                </ol>
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
                    {{ formatDate(selectedScholarship?.application_period_end || selectedScholarship?.application_deadline) }}
                  </p>
                  <p class="text-xs text-orange-600">
                    {{
                      getDaysRemaining(
                        selectedScholarship?.application_period_end || selectedScholarship?.application_deadline,
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

          <Alert v-if="!canSubmit && props.application?.status !== 'approved'" variant="warning">
            <Icon icon="lucide:alert-triangle" class="h-4 w-4" />
            <AlertTitle>Application Not Yet Approved</AlertTitle>
            <AlertDescription>
              You can only apply for scholarships after your main application has been approved by our team.
            </AlertDescription>
          </Alert>

          <Alert v-else-if="!canSubmit" variant="warning">
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
      <SheetContent class="w-full sm:max-w-lg p-0 flex flex-col">
        <SheetHeader class="p-6 border-b">
          <SheetTitle class="flex items-center text-lg">
            <Icon icon="lucide:folder-up" class="mr-2 h-5 w-5 text-primary" />
            Manage Your Documents
          </SheetTitle>
          <SheetDescription>
            Upload and manage required and optional documents for your application.
            Ensure all required items are submitted and approved.
          </SheetDescription>
        </SheetHeader>

        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6">
          <!-- Progress Summary -->
          <div class="p-4 rounded-lg border bg-muted/50">
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
          <div class="space-y-2">
            <h3 class="text-base font-semibold text-foreground mb-2">
              Required Documents
            </h3>
            <Accordion type="single" collapsible class="w-full space-y-2">
              <AccordionItem
                v-for="docType in documentTypes.filter((d) => d.required)"
                :key="docType.type"
                :value="docType.type"
                class="border rounded-lg overflow-hidden shadow-sm bg-background hover:bg-muted/50 dark:hover:bg-muted/20 transition-colors duration-150"
              >
                <AccordionTrigger
                  class="px-4 py-3 text-sm font-medium w-full [&[data-state=open]>div>svg.trigger-icon]:rotate-180 hover:no-underline focus:no-underline"
                >
                  <div class="flex items-center justify-between w-full gap-2">
                    <div class="flex items-center gap-3 min-w-0">
                      <Icon
                        :icon="getIconForDocType(docType)"
                        :class="getColorForDocTypeStatus(docType)"
                        class="h-6 w-6 flex-shrink-0 transition-colors duration-300"
                      />
                      <div class="text-left">
                        <span class="font-semibold text-foreground">{{ docType.label }}</span>
                        <p v-if="docType.requires_semester && scholarSemesterInfo?.type" class="text-xs text-muted-foreground">
                          <Icon icon="lucide:calendar-days" class="inline-block h-3 w-3 mr-0.5"/> Semester-specific uploads
                        </p>
                      </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <Badge v-if="getDocumentStatusInfo(docType).text" :variant="getDocumentStatusInfo(docType).variant" size="sm">
                            {{ getDocumentStatusInfo(docType).text }}
                        </Badge>
                        <Icon
                            icon="lucide:chevron-down"
                            class="h-4 w-4 shrink-0 transition-transform duration-200 trigger-icon text-muted-foreground"
                        />
                    </div>
                  </div>
                </AccordionTrigger>
                <AccordionContent class="px-4 pt-2 pb-4 bg-muted/50 dark:bg-muted/20 border-t">
                  <p class="text-xs text-muted-foreground mb-3">
                    {{ docType.description }}
                  </p>

                  <div v-if="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)" class="mb-3 text-xs p-3 rounded-md border"
                    :class="{
                        'bg-green-50 dark:bg-green-500/10 border-green-200 dark:border-green-500/30 text-green-700 dark:text-green-300': getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status === 'approved',
                        'bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/30 text-blue-700 dark:text-blue-300': getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year) && getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status !== 'approved' && getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status !== 'rejected',
                        'bg-red-50 dark:bg-red-500/10 border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-300': getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status === 'rejected',
                    }">
                    <p class="font-medium mb-0.5 break-all">
                        <Icon icon="lucide:paperclip" class="inline-block mr-1 h-3 w-3" />
                        Filename: {{ getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.original_name || 'N/A' }}
                    </p>
                    <p>Uploaded: {{ formatDate(getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.created_at) }}</p>
                    <p v-if="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.notes" class="mt-1 pt-1 border-t border-dashed">
                        Admin Notes: {{ getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.notes }}
                    </p>
                  </div>

                  <Button
                    :variant="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year) ? 'outline' : 'default'"
                    size="sm"
                    class="w-full sm:w-auto"
                    @click="openFileUpload(docType.type)"
                    :disabled="uploadingFile && currentDocumentType === docType.type"
                  >
                    <Icon
                      v-if="uploadingFile && currentDocumentType === docType.type"
                      icon="lucide:loader-2"
                      class="mr-1.5 h-4 w-4 animate-spin"
                    />
                    <Icon
                      v-else
                      :icon="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year) ? (getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status === 'rejected' ? 'lucide:upload-cloud' : 'lucide:replace') : 'lucide:upload-cloud'"
                      class="mr-1.5 h-4 w-4"
                    />
                    {{ getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year) ? (getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status === 'rejected' ? 'Re-upload' : 'Replace File') : 'Upload File' }}
                  </Button>
                </AccordionContent>
              </AccordionItem>
            </Accordion>
          </div>

          <!-- Optional Documents Accordion (similar styling changes) -->
          <div class="space-y-2">
            <h3 class="text-base font-semibold text-foreground mb-2">
              Optional Documents
            </h3>
            <Accordion type="single" collapsible class="w-full space-y-2">
              <AccordionItem
                v-for="docType in documentTypes.filter((d) => !d.required)"
                :key="docType.type"
                :value="docType.type"
                class="border rounded-lg overflow-hidden shadow-sm bg-background hover:bg-muted/50 dark:hover:bg-muted/20 transition-colors duration-150"
              >
                <AccordionTrigger
                   class="px-4 py-3 text-sm font-medium w-full [&[data-state=open]>div>svg.trigger-icon]:rotate-180 hover:no-underline focus:no-underline"
                >
                  <div class="flex items-center justify-between w-full gap-2">
                     <div class="flex items-center gap-3 min-w-0">
                        <Icon
                            :icon="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year) ? 'lucide:file-check-2' : 'lucide:file-plus-2'"
                            :class="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year) ? 'text-primary' : 'text-muted-foreground'"
                            class="h-5 w-5 flex-shrink-0"
                        />
                        <span class="truncate">{{ docType.label }}</span>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <Badge v-if="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)" :variant="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status === 'approved' ? 'success' : (getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status === 'rejected' ? 'destructive' : 'info')" size="sm">
                             {{ getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status?.replace('_', ' ') || 'Uploaded' }}
                        </Badge>
                        <Badge v-else variant="outline" size="sm">Optional</Badge>
                        <Icon
                            icon="lucide:chevron-down"
                            class="h-4 w-4 shrink-0 transition-transform duration-200 trigger-icon"
                        />
                    </div>
                  </div>
                </AccordionTrigger>
                <AccordionContent class="px-4 pt-2 pb-4 bg-muted/50 dark:bg-muted/20 border-t">
                  <p class="text-xs text-muted-foreground mb-3">
                    {{ docType.description }}
                  </p>
                    <div v-if="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)" class="mb-3 text-xs p-3 rounded-md border"
                    :class="{
                        'bg-green-50 dark:bg-green-500/10 border-green-200 dark:border-green-500/30 text-green-700 dark:text-green-300': getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status === 'approved',
                        'bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/30 text-blue-700 dark:text-blue-300': getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year) && getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status !== 'approved' && getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status !== 'rejected',
                        'bg-red-50 dark:bg-red-500/10 border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-300': getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status === 'rejected',
                         'bg-gray-50 dark:bg-gray-500/10 border-gray-200 dark:border-gray-500/30 text-gray-700 dark:text-gray-300': !getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status, // Default for just uploaded
                    }">
                        <p class="font-medium mb-0.5 break-all">
                            <Icon icon="lucide:paperclip" class="inline-block mr-1 h-3 w-3" />
                            Filename: {{ getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.original_name || 'N/A' }}
                        </p>
                        <p>Uploaded: {{ formatDate(getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.created_at) }}</p>
                         <p v-if="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.notes" class="mt-1 pt-1 border-t border-dashed">
                            Admin Notes: {{ getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.notes }}
                        </p>
                    </div>
                  <Button
                    :variant="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year) ? 'outline' : 'default'"
                    size="sm"
                    class="w-full sm:w-auto"
                    @click="openFileUpload(docType.type)"
                    :disabled="uploadingFile && currentDocumentType === docType.type"
                  >
                     <Icon
                      v-if="uploadingFile && currentDocumentType === docType.type"
                      icon="lucide:loader-2"
                      class="mr-1.is_numeric(val) h-4 w-4 animate-spin"
                    />
                    <Icon
                      v-else
                      :icon="getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year) ? (getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status === 'rejected' ? 'lucide:upload-cloud' : 'lucide:replace') : 'lucide:upload-cloud'"
                      class="mr-1.5 h-4 w-4"
                    />
                    {{ getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year) ? (getDocumentByTypeAndSemester(docType.type, docType.semester_type, docType.semester_number, docType.academic_year)?.status === 'rejected' ? 'Re-upload Optional' : 'Replace Optional File') : 'Upload Optional File' }}
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
          <!-- Semester information will be collected in the UI before uploading -->
        </form>

        <!-- Add a Semester Information Form Dialog -->
        <Dialog v-model:open="showSemesterSelector">
          <DialogContent class="sm:max-w-md">
            <DialogHeader>
              <DialogTitle class="flex items-center">
                <Icon icon="lucide:calendar-days" class="h-5 w-5 mr-2 text-primary" />
                Semester Information
              </DialogTitle>
              <DialogDescription>
                Please select the semester this document belongs to.
              </DialogDescription>
            </DialogHeader>
            
            <div class="py-4 space-y-4">
              <div class="space-y-2">
                <Label for="semester-select">Academic Period</Label>
                <Select v-model="selectedSemester">
                  <SelectTrigger>
                    <SelectValue placeholder="Select semester/trimester" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup v-for="(group, index) in semesterOptionsByYear" :key="index" :label="group.year">
                      <SelectItem 
                        v-for="option in group.options" 
                        :key="`${option.value.academic_year}-${option.value.semester_number}`"
                        :value="option.value"
                      >
                        {{ option.label }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
                <p class="text-xs text-muted-foreground mt-1">
                  <Icon icon="lucide:info" class="h-3.5 w-3.5 inline mr-1" />
                  {{ currentDocumentType ? currentDocumentType.replace(/_/g, ' ') : 'Document' }} for each semester/trimester is required.
                </p>
              </div>
            </div>
            
            <DialogFooter>
              <Button variant="outline" @click="showSemesterSelector = false">
                Cancel
              </Button>
              <Button 
                @click="confirmSemesterSelection"
                :disabled="!selectedSemester"
              >
                <Icon icon="lucide:check" class="h-4 w-4 mr-1.5" />
                Continue with Upload
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
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
