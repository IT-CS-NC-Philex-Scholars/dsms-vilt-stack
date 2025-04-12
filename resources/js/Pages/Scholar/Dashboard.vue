<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Application Progress
      </h2>
    </template>

    <div class="container py-8">
      <div class="max-w-3xl mx-auto space-y-8">
        <div v-if="application" class="space-y-8">
          <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Scholarship Application</h1>
            <!-- Use the computed applicationStatus -->
            <Badge
              v-if="applicationStatus"
              :variant="applicationStatus.variant"
            >
              {{ applicationStatus.label }}
            </Badge>
          </div>

          <!-- Display message if application is submitted/approved/rejected -->
          <div
            v-if="application.status !== 'draft'"
            class="p-4 border rounded-lg bg-secondary/20"
          >
            <p class="text-muted-foreground">
              <template v-if="application.status === 'pending_review'">
                Your application has been submitted and is currently under
                review. You will be notified once a decision has been made.
              </template>
              <template v-else-if="application.status === 'approved'">
                Congratulations! Your application has been approved. Check your
                notifications or email for further instructions.
              </template>
              <template v-else-if="application.status === 'rejected'">
                We regret to inform you that your application was not approved
                at this time. Reason:
                {{ application.rejection_reason || "Not specified" }}
              </template>
              <template v-else>
                <!-- Handle other potential statuses -->
                Current status: {{ applicationStatus.label }}.
              </template>
            </p>
          </div>

          <!-- Only show upload section if status is 'draft' -->
          <template v-if="application.status === 'draft'">
            <!-- Application Progress -->
            <div class="space-y-4">
              <h2 class="text-lg font-semibold">Required Documents</h2>
              <p class="text-sm text-muted-foreground">
                Please upload all required documents to proceed with your
                application.
              </p>
              <div class="grid gap-4">
                <!-- Use the computed requiredDocuments -->
                <div
                  v-for="doc in requiredDocuments"
                  :key="doc.type"
                  class="p-4 border rounded-lg flex items-center justify-between gap-4"
                  :class="{ 'bg-green-50 border-green-200': doc.uploaded }"
                >
                  <div class="flex-1">
                    <h3 class="font-medium">{{ doc.label }}</h3>
                    <p class="text-sm text-muted-foreground">
                      {{ doc.description }}
                    </p>
                    <!-- Display filename if uploaded -->
                    <p
                      v-if="doc.uploaded && doc.original_name"
                      class="text-xs text-green-700 mt-1 italic truncate"
                    >
                      Uploaded: {{ doc.original_name }}
                    </p>
                    <!-- Display upload errors -->
                    <p
                      v-if="uploadForm.errors[`document_${doc.type}`]"
                      class="text-xs text-destructive mt-1"
                    >
                      {{ uploadForm.errors[`document_${doc.type}`] }}
                    </p>
                  </div>
                  <!-- Conditional Upload/Replace Button -->
                  <Button
                    :variant="doc.uploaded ? 'outline' : 'default'"
                    size="sm"
                    @click="triggerFileUpload(doc)"
                    :disabled="uploadForm.processing"
                  >
                    <Icon
                      v-if="!uploadForm.processing"
                      :icon="
                        doc.uploaded ? 'lucide:refresh-cw' : 'lucide:upload'
                      "
                      class="h-4 w-4 mr-2"
                    />
                    <Icon
                      v-else
                      icon="lucide:loader-2"
                      class="h-4 w-4 mr-2 animate-spin"
                    />
                    {{ doc.uploaded ? "Replace" : "Upload" }}
                  </Button>
                </div>
                <!-- Hidden file input -->
                <input
                  type="file"
                  ref="fileInputRef"
                  @change="handleFileChange"
                  class="hidden"
                  :accept="currentUploadAccept"
                />
              </div>
              <!-- Upload Progress -->
              <div
                v-if="uploadForm.progress"
                class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700"
              >
                <div
                  class="bg-blue-600 h-2.5 rounded-full"
                  :style="{ width: uploadForm.progress.percentage + '%' }"
                ></div>
              </div>
            </div>

            <!-- Submit Application -->
            <div class="flex justify-end">
              <Button
                :disabled="!canSubmit || submitApplicationForm.processing"
                @click="submitApplication"
                :title="
                  !canSubmit ? 'Please upload all required documents first' : ''
                "
              >
                <Icon
                  v-if="submitApplicationForm.processing"
                  icon="lucide:loader-2"
                  class="h-4 w-4 mr-2 animate-spin"
                />
                <Icon v-else icon="lucide:send" class="h-4 w-4 mr-2" />
                Submit Application
              </Button>
            </div>
          </template>
          <!-- End v-if application.status === 'draft' -->
        </div>
        <!-- End v-if application -->
        <div v-else class="text-center py-10">
          <p class="text-muted-foreground">
            Loading application data or no application found...
          </p>
          <!-- Optional: Add a button to start application if none exists -->
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import Badge from "@/Components/shadcn/ui/badge/Badge.vue";
import Button from "@/Components/shadcn/ui/button/Button.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { Icon } from "@iconify/vue";
import { toast } from "vue-sonner"; // Assuming you use vue-sonner

const props = defineProps({
  application: {
    type: Object,
    required: false, // Make it not strictly required in case loading fails or none exists yet
    default: null,
  },
  // Optional: Pass definitions from controller if they become dynamic
  // requiredDocumentDefinitions: {
  //   type: Array,
  //   default: () => [...]
  // }
});

const page = usePage();

// --- Application Status ---
const applicationStatus = computed(() => {
  const status = props.application?.status;
  if (!status) return { label: "Loading...", variant: "outline" }; // Handle null application case

  switch (status) {
    case "draft":
      return { label: "Draft", variant: "secondary" };
    case "pending_review":
      return { label: "Pending Review", variant: "warning" };
    case "approved":
      return { label: "Approved", variant: "success" };
    case "rejected":
      return { label: "Rejected", variant: "destructive" };
    // Add more statuses if needed
    default:
      return { label: status, variant: "outline" }; // Display unknown status
  }
});

// --- Document Handling ---

// Base definitions (could come from props later)
const documentDefinitions = ref([
  {
    type: "residency",
    label: "Proof of Residency",
    description: "e.g., Barangay Certificate (PDF, JPG, JPEG)",
    accept: ".pdf,.jpg,.jpeg",
  },
  {
    type: "enrollment",
    label: "Proof of Enrollment",
    description: "e.g., Enrollment/Registration Form (PDF)",
    accept: ".pdf",
  },
  {
    type: "grades",
    label: "Academic Records",
    description: "e.g., Transcript of Records, Latest Report Card (PDF)",
    accept: ".pdf",
  },
]);

// Computed property to merge definitions with actual upload status and filename
const requiredDocuments = computed(() => {
  if (!props.application?.documents)
    return documentDefinitions.value.map((def) => ({
      ...def,
      uploaded: false,
      original_name: null,
    }));

  const uploadedDocsMap = new Map(
    props.application.documents.map((doc) => [doc.type, doc]),
  );

  return documentDefinitions.value.map((def) => {
    const uploadedDoc = uploadedDocsMap.get(def.type);
    return {
      ...def,
      uploaded: !!uploadedDoc,
      original_name: uploadedDoc?.original_name || null,
    };
  });
});

const fileInputRef = ref(null);
const currentUploadType = ref(null);
const currentUploadAccept = ref("*"); // Default accept type

const uploadForm = useForm({
  document: null,
  type: null,
});

const triggerFileUpload = (docDefinition) => {
  currentUploadType.value = docDefinition.type;
  currentUploadAccept.value = docDefinition.accept;
  // Clear previous errors specific to this type if any
  uploadForm.clearErrors(`document_${docDefinition.type}`);
  fileInputRef.value?.click(); // Trigger the hidden file input
};

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (!file || !currentUploadType.value) {
    // Clear the file input value in case the user cancels
    if (event.target) event.target.value = null;
    return;
  }

  uploadForm.type = currentUploadType.value;
  uploadForm.document = file;

  // Clear previous generic errors before posting
  uploadForm.clearErrors("document", "type");

  uploadForm.post(route("scholar.upload-document"), {
    preserveScroll: true,
    forceFormData: true, // Important for file uploads with Inertia forms
    onSuccess: () => {
      toast.success(
        `${currentUploadType.value.charAt(0).toUpperCase() + currentUploadType.value.slice(1)} document uploaded successfully!`,
      );
      // Props will automatically update via Inertia reload
    },
    onError: (errors) => {
      console.error("Upload failed:", errors);
      // Construct a specific error key for display, if needed
      const errorKey = `document_${currentUploadType.value}`;
      const specificError =
        errors.document || errors.type || "Upload failed. Please try again.";
      uploadForm.setError(errorKey, specificError); // Set error for specific field display
      toast.error(specificError);
    },
    onFinish: () => {
      // Reset file input and form state after completion
      if (event.target) event.target.value = null; // Clear file input
      uploadForm.reset("document"); // Reset only the file field in the form helper
      currentUploadType.value = null; // Reset current type
    },
  });
};

// --- Application Submission ---
const canSubmit = computed(() => {
  if (!props.application || props.application.status !== "draft") return false;
  // Check if the number of *actually* uploaded documents matches the number of required definitions
  const uploadedCount = props.application.documents?.length ?? 0;
  return uploadedCount >= documentDefinitions.value.length;
});

const submitApplicationForm = useForm({}); // Form helper for submission state/errors

const submitApplication = () => {
  if (!canSubmit.value) {
    toast.error("Please upload all required documents before submitting.");
    return;
  }

  submitApplicationForm.post(route("scholar.submit-application"), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success(
        "Application submitted successfully! It is now under review.",
      );
      // Props update will change the view (hide upload section etc.)
    },
    onError: (errors) => {
      console.error("Submission failed:", errors);
      const errorMessage =
        errors.message ||
        page.props.errors?.error ||
        "Failed to submit application. Please try again.";
      toast.error(errorMessage);
    },
  });
};

// --- Flash Message Handling ---
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
); // Check immediately on load
</script>
