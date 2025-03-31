<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Application Progress
      </h2>
    </template>

    <div class="container py-8">
      <div class="max-w-3xl mx-auto space-y-8">
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-bold">Scholarship Application</h1>
          <Badge :variant="applicationStatus.variant">
            {{ applicationStatus.label }}
          </Badge>
        </div>

        <!-- Application Progress -->
        <div class="space-y-4">
          <h2 class="text-lg font-semibold">Required Documents</h2>
          <div class="grid gap-4">
            <div
              v-for="doc in requiredDocuments"
              :key="doc.type"
              class="p-4 border rounded-lg"
            >
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-medium">{{ doc.label }}</h3>
                  <p class="text-sm text-muted-foreground">
                    {{ doc.description }}
                  </p>
                </div>
                <Button v-if="!doc.uploaded" @click="uploadDocument(doc.type)">
                  Upload
                </Button>
                <Badge v-else variant="success">Uploaded</Badge>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Application -->
        <div class="flex justify-end">
          <Button :disabled="!canSubmit" @click="submitApplication">
            Submit Application
          </Button>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script setup>
import { ref, computed } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  application: {
    type: Object,
    required: true,
  },
});

const steps = [
  "Pre-qualified",
  "Documents Uploaded",
  "Under Review",
  "Decision",
];
const currentStep = computed(() => {
  if (props.application.status === "approved") return 4;
  if (props.application.status === "pending_review") return 3;
  if (props.application.documents_uploaded) return 2;
  return 1;
});

const requiredDocs = ref([
  {
    type: "residency",
    label: "Proof of Residency",
    description: "Barangay Certificate (PDF or JPG)",
    uploaded: false,
  },
  {
    type: "enrollment",
    label: "Proof of Enrollment",
    description: "Enrollment letter or registration form",
    uploaded: false,
  },
  {
    type: "grades",
    label: "Academic Records",
    description: "Transcript of Records (PDF)",
    uploaded: false,
  },
]);

const canSubmit = computed(() => {
  return requiredDocs.value.every((doc) => doc.uploaded);
});

const uploadDocument = async (type) => {
  // This will be implemented with file upload logic
  const input = document.createElement("input");
  input.type = "file";
  input.accept = type === "residency" ? ".pdf,.jpg,.jpeg" : ".pdf";

  input.onchange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const form = new FormData();
    form.append("document", file);
    form.append("type", type);

    try {
      await axios.post(route("scholar.upload-document"), form);
      // Update the UI to show the document as uploaded
      const doc = requiredDocs.value.find((d) => d.type === type);
      if (doc) doc.uploaded = true;
    } catch (error) {
      console.error("Upload failed:", error);
    }
  };

  input.click();
};

const submitApplication = async () => {
  if (!canSubmit.value) return;

  try {
    await axios.post(route("scholar.submit-application"));
    // Handle success (redirect or show success message)
  } catch (error) {
    console.error("Submission failed:", error);
  }
};
</script>
