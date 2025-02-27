<script setup>
import FeaturesCard from "@/Components/FeaturesCard.vue";
import PricingCard from "@/Components/PricingCard.vue";
import Accordion from "@/Components/shadcn/ui/accordion/Accordion.vue";
import AccordionContent from "@/Components/shadcn/ui/accordion/AccordionContent.vue";
import AccordionItem from "@/Components/shadcn/ui/accordion/AccordionItem.vue";
import AccordionTrigger from "@/Components/shadcn/ui/accordion/AccordionTrigger.vue";
import Badge from "@/Components/shadcn/ui/badge/Badge.vue";
import Button from "@/Components/shadcn/ui/button/Button.vue";
import Terminal from "@/Components/Terminal.vue";
import { useSeoMetaTags } from "@/Composables/useSeoMetaTags.js";
import WebLayout from "@/Layouts/WebLayout.vue";
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/Components/shadcn/ui/card";
import { ScrollArea } from "@/Components/shadcn/ui/scroll-area";
import {
  TabsContent,
  TabsList,
  TabsTrigger,
  Tabs,
} from "@/Components/shadcn/ui/tabs";

const props = defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
  seo: {
    type: Object,
    default: () => null,
  },
});

useSeoMetaTags(props.seo);

const features = [
  {
    icon: "📝",
    title: "Easy Application Process",
    description:
      "Streamlined scholarship application process with intuitive forms and document upload capabilities.",
  },
  {
    icon: "🔍",
    title: "Application Tracking",
    description:
      "Real-time tracking of application status, requirements completion, and approval stages.",
  },
  {
    icon: "📊",
    title: "Progress Monitoring",
    description:
      "Comprehensive dashboard to monitor academic performance, requirements, and scholarship status.",
  },
  {
    icon: "📄",
    title: "Document Management",
    description:
      "Secure storage and management of academic records, certificates, and other required documents.",
  },
  {
    icon: "📱",
    title: "Communication Hub",
    description:
      "Integrated messaging system for updates, announcements, and direct communication with administrators.",
  },
  {
    icon: "⚡",
    title: "Quick Requirements Review",
    description:
      "Efficient system for reviewing and approving submitted requirements with automated notifications.",
  },
  {
    icon: "📈",
    title: "Performance Analytics",
    description:
      "Detailed analytics and reporting tools for tracking scholar performance and program effectiveness.",
  },
  {
    icon: "🔐",
    title: "Secure Access",
    description:
      "Role-based access control ensuring data privacy and secure information handling.",
  },
  {
    icon: "🎓",
    title: "Academic Support",
    description:
      "Tools for monitoring academic progress and providing timely support to scholars.",
  },
];

const systemFeatures = [
  "Easy application submission",
  "Real-time status tracking",
  "Secure document management",
  "Automated notifications",
  "Performance monitoring",
  "Communication tools",
];

const faqCategories = [
  {
    id: "general",
    icon: "lucide:info",
    title: "General Information",
    questions: [
      {
        question: "What types of scholarships are available?",
        answer: "We offer several types of scholarships including:",
        details: [
          "Academic Excellence Scholarships",
          "Financial Need-based Grants",
          "Mining Community Development Scholarships",
          "Special Skills and Talents Grants",
        ],
      },
      {
        question: "Who is eligible to apply?",
        answer:
          "Eligibility varies by program, but general requirements include:",
        details: [
          "Must be a resident of covered mining communities",
          "Enrolled or planning to enroll in an accredited institution",
          "Maintaining required academic standing",
          "Demonstrated financial need",
        ],
      },
    ],
  },
  {
    id: "application",
    icon: "lucide:file-text",
    title: "Application Process",
    questions: [
      {
        question: "What documents do I need to prepare?",
        answer: "Essential documents for application include:",
        details: [
          "Valid government ID",
          "Latest grade reports/transcript",
          "Proof of enrollment/admission",
          "Family income certificate",
          "Recommendation letters",
          "Personal statement",
        ],
      },
      {
        question: "How long does the application process take?",
        answer: "The typical timeline breaks down as follows:",
        details: [
          "Initial application review: 1-2 weeks",
          "Document verification: 1 week",
          "Committee evaluation: 2 weeks",
          "Final decision: 1 week",
          "Total process: Approximately 4-6 weeks",
        ],
      },
    ],
  },
  {
    id: "financial",
    icon: "lucide:wallet",
    title: "Financial Aspects",
    questions: [
      {
        question: "How much financial support is provided?",
        answer: "Scholarship amounts vary by program and level:",
        details: [
          "High School: Up to ₱30,000 per year",
          "Undergraduate: Up to ₱50,000 per year",
          "Additional allowances for books and supplies",
          "Special grants for exceptional cases",
        ],
      },
      {
        question: "How are payments disbursed?",
        answer: "Scholarship funds are released through:",
        details: [
          "Direct deposit to student account",
          "Semestral disbursement schedule",
          "Performance-based installments",
          "Monitoring of fund utilization",
        ],
      },
    ],
  },
  {
    id: "maintenance",
    icon: "lucide:award",
    title: "Maintaining Your Scholarship",
    questions: [
      {
        question: "What are the requirements to maintain my scholarship?",
        answer: "To maintain your scholarship status, you need to:",
        details: [
          "Maintain minimum GPA requirements",
          "Submit regular progress reports",
          "Participate in community programs",
          "Comply with attendance requirements",
        ],
      },
      {
        question: "Can my scholarship be renewed?",
        answer: "Scholarship renewal depends on:",
        details: [
          "Academic performance",
          "Compliance with program requirements",
          "Available funding",
          "Continued eligibility",
        ],
      },
    ],
  },
];
import { ref } from "vue";

const activeCategory = ref(faqCategories[0].id);
const activeQuestion = ref(null);
const applicationSteps = [
  {
    number: 1,
    title: "Create Your Account",
    description:
      "Sign up and verify your email to access the application portal",
    icon: "lucide:user-plus",
    tips: [
      "Use an email you check regularly",
      "Create a strong password",
      "Keep your login credentials secure",
    ],
  },
  {
    number: 2,
    title: "Complete Your Profile",
    description: "Fill in your personal and academic information",
    icon: "lucide:pencil",
    tips: [
      "Have your basic information ready",
      "Be accurate with your details",
      "Double-check all entries",
    ],
  },
  {
    number: 3,
    title: "Submit Requirements",
    description: "Upload all necessary documents and forms",
    icon: "lucide:file-plus",
    tips: [
      "Prepare documents in advance",
      "Ensure clear, readable scans",
      "Check file size limits",
    ],
  },
  {
    number: 4,
    title: "Application Review",
    description: "Wait for the evaluation of your application",
    icon: "lucide:check-circle",
    tips: [
      "Monitor your dashboard regularly",
      "Respond promptly to any queries",
      "Keep documents updated",
    ],
  },
];
const applicationUrl = "/apply-now";
</script>

<template>
  <WebLayout :can-login="canLogin" :can-register="canRegister">
    <!-- Hero Section -->
    <section
      class="relative overflow-hidden border-b bg-background py-20 sm:py-32"
    >
      <div class="container mx-auto px-4 text-center">
        <!-- Badge -->
        <div class="mb-8 inline-flex justify-center">
          <Badge
            variant="outline"
            class="rounded-full border bg-primary/10 px-4 py-1 text-xs sm:text-sm"
          >
            ✨ Streamlined Scholarship Management System
          </Badge>
        </div>

        <!-- Main Heading -->
        <div class="mx-auto max-w-4xl">
          <h1
            class="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl lg:text-7xl"
            :style="{ contain: 'layout paint' }"
          >
            <span class="block text-foreground">ScholarTrack</span>
            <span
              class="mt-2 block bg-linear-to-r from-blue-500 via-purple-500 to-indigo-500 bg-clip-text text-transparent"
            >
              Application & Progress Tracking
            </span>
          </h1>
        </div>

        <!-- Subtitle -->
        <p
          class="mx-auto mt-6 max-w-2xl text-center text-base text-muted-foreground sm:text-lg md:text-xl"
          :style="{ contain: 'layout paint' }"
          fetchpriority="high"
        >
          Simplifying scholarship management with comprehensive tracking,
          monitoring, and support tools.
        </p>

        <!-- CTA Buttons -->
        <div class="mt-10 flex items-center justify-center gap-4 flex-row">
          <Button
            as="a"
            :href="applicationUrl"
            size="lg"
            class="w-full sm:w-auto"
          >
            Apply Now
          </Button>
          <Button
            as="a"
            :href="route('login')"
            size="lg"
            variant="outline"
            class="w-full sm:w-auto"
          >
            <Icon icon="lucide:user" class="size-4" aria-hidden="true" />
            Track Application
          </Button>
        </div>

        <!-- Trust Indicators -->
        <div class="mt-16 sm:mt-24">
          <p class="text-sm text-muted-foreground">
            Trusted by educational institutions
          </p>
          <div
            class="mt-4 flex flex-wrap items-center justify-center gap-6 sm:gap-8"
          >
            <!-- Add relevant educational institution logos here -->
          </div>
        </div>
      </div>

      <!-- Background Effects -->
      <div
        class="absolute inset-0 -z-10 h-full w-full bg-[linear-gradient(to_right,#4f4f4f2e_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f2e_1px,transparent_1px)] bg-[size:14px_24px]"
      />
      <div
        class="absolute left-0 right-0 top-0 -z-10 m-auto h-[310px] w-[310px] rounded-full bg-primary/20 opacity-20 blur-[100px]"
      />
    </section>

    <!-- Features Grid -->
    <section id="features" class="container mx-auto px-4 py-16 sm:px-6 lg:px-8">
      <h2 class="text-center text-2xl font-bold tracking-tight sm:text-4xl">
        System Features ✨
      </h2>
      <p class="mx-auto mt-4 max-w-2xl text-center text-muted-foreground">
        Everything you need to manage your scholarship application and track
        your progress.
      </p>

      <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <FeaturesCard
          v-for="feature in features"
          :key="feature.title"
          :icon="feature.icon"
          :title="feature.title"
          :description="feature.description"
        />
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="border-t">
      <div class="container mx-auto px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl text-center">
          <h2 class="text-center text-2xl font-bold tracking-tight sm:text-4xl">
            Frequently Asked Questions
          </h2>
          <p class="mt-4 text-muted-foreground">
            Find answers to common questions about our scholarship programs and
            application process
          </p>
        </div>

        <div class="mt-16">
          <div class="grid gap-8 lg:grid-cols-[300px_1fr]">
            <!-- Category Navigation -->
            <Card class="h-fit">
              <CardContent class="p-4">
                <nav class="space-y-2">
                  <button
                    v-for="category in faqCategories"
                    :key="category.id"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors hover:bg-accent"
                    :class="{ 'bg-accent': activeCategory === category.id }"
                    @click="activeCategory = category.id"
                  >
                    <Icon :icon="category.icon" class="h-5 w-5" />
                    <span>{{ category.title }}</span>
                  </button>
                </nav>
              </CardContent>
            </Card>

            <!-- FAQ Content -->
            <Card>
              <CardHeader>
                <CardTitle>
                  {{
                    faqCategories.find((c) => c.id === activeCategory)?.title
                  }}
                </CardTitle>
                <CardDescription>
                  Click on any question to see the detailed answer
                </CardDescription>
              </CardHeader>
              <CardContent class="p-6">
                <ScrollArea class="h-[500px] pr-4">
                  <div class="space-y-4">
                    <div
                      v-for="(category, idx) in faqCategories"
                      :key="idx"
                      v-show="category.id === activeCategory"
                    >
                      <div
                        v-for="(item, qIdx) in category.questions"
                        :key="qIdx"
                        class="rounded-lg border transition-all duration-200 hover:shadow-md"
                        :class="{
                          'border-primary':
                            activeQuestion === `${category.id}-${qIdx}`,
                        }"
                      >
                        <button
                          class="flex w-full items-center justify-between p-4 text-left"
                          @click="
                            activeQuestion =
                              activeQuestion === `${category.id}-${qIdx}`
                                ? null
                                : `${category.id}-${qIdx}`
                          "
                        >
                          <h3 class="font-medium">{{ item.question }}</h3>
                          <Icon
                            :icon="
                              activeQuestion === `${category.id}-${qIdx}`
                                ? 'lucide:minus'
                                : 'lucide:plus'
                            "
                            class="h-5 w-5 shrink-0"
                          />
                        </button>

                        <div
                          v-if="activeQuestion === `${category.id}-${qIdx}`"
                          class="border-t px-4 py-3"
                        >
                          <p class="text-sm text-muted-foreground">
                            {{ item.answer }}
                          </p>
                          <ul class="mt-2 space-y-1">
                            <li
                              v-for="(detail, dIdx) in item.details"
                              :key="dIdx"
                              class="flex items-center gap-2 text-sm text-muted-foreground"
                            >
                              <Icon
                                icon="lucide:check"
                                class="h-4 w-4 text-green-500"
                              />
                              {{ detail }}
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </ScrollArea>
              </CardContent>
            </Card>
          </div>
        </div>

        <!-- Help Center Link -->
        <div class="mt-12 text-center">
          <p class="text-sm text-muted-foreground">
            Can't find what you're looking for?
          </p>
          <Button variant="link" as="a" href="/help-center" class="mt-2">
            Visit our Help Center
            <Icon icon="lucide:external-link" class="ml-2 h-4 w-4" />
          </Button>
        </div>
      </div>
    </section>

    <!-- New How to Apply Section -->
    <section id="how-to-apply" class="border-t">
      <div class="container mx-auto px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl text-center">
          <h2 class="text-center text-2xl font-bold tracking-tight sm:text-4xl">
            How to Apply
          </h2>
          <p class="mt-4 text-muted-foreground">
            Follow these simple steps to start your scholarship journey
          </p>
        </div>

        <div class="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
          <Card
            v-for="step in applicationSteps"
            :key="step.number"
            class="relative"
          >
            <CardHeader>
              <div
                class="absolute -top-4 left-4 flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white"
              >
                {{ step.number }}
              </div>
              <Icon :icon="step.icon" class="h-8 w-8 text-primary" />
              <CardTitle>{{ step.title }}</CardTitle>
              <CardDescription>{{ step.description }}</CardDescription>
            </CardHeader>
            <CardContent>
              <ul class="list-disc pl-4 text-sm text-muted-foreground">
                <li v-for="(tip, index) in step.tips" :key="index" class="mt-2">
                  {{ tip }}
                </li>
              </ul>
            </CardContent>
          </Card>
        </div>

        <div class="mt-12 text-center">
          <Button as="a" :href="applicationUrl" size="lg" class="gap-2">
            <Icon icon="lucide:arrow-right" class="h-4 w-4" />
            Start Your Application
          </Button>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="border-t">
      <div class="container mx-auto px-4 py-16 sm:px-6 lg:px-8">
        <div class="rounded-2xl px-6 py-12 sm:p-16">
          <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight sm:text-6xl">
              Ready to start your journey?
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-lg">
              Take the first step towards your educational goals.<br />
              Apply for your scholarship today! 🎓
            </p>
            <div class="mt-8 flex justify-center gap-4">
              <Button as="a" :href="applicationUrl" size="lg">
                Apply Now
              </Button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </WebLayout>
</template>
