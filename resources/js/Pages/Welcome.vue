<script setup>
// Keep necessary Shadcn UI components
import Accordion from "@/Components/shadcn/ui/accordion/Accordion.vue";
import AccordionContent from "@/Components/shadcn/ui/accordion/AccordionContent.vue";
import AccordionItem from "@/Components/shadcn/ui/accordion/AccordionItem.vue";
import AccordionTrigger from "@/Components/shadcn/ui/accordion/AccordionTrigger.vue";
import Badge from "@/Components/shadcn/ui/badge/Badge.vue";
import Button from "@/Components/shadcn/ui/button/Button.vue";
import { ChevronDown } from "lucide-vue-next";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/Components/shadcn/ui/card"; // Import Card components
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from "@/Components/shadcn/ui/tabs";
// Keep utilities
import { useSeoMetaTags } from "@/Composables/useSeoMetaTags.js";
import WebLayout from "@/Layouts/WebLayout.vue";
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
  appName: {
    type: String,
    required: true,
  },
  phpVersion: {
    type: String,
    required: true,
  },
  laravelVersion: {
    type: String,
    required: true,
  },
  seo: {
    type: Object,
    default: () => null,
  },
});

useSeoMetaTags(props.seo);

// --- Define PhilexScholar Content ---

// Features based on README
const studentFeatures = [
  {
    icon: "lucide:edit",
    title: "Smart Application Portal",
    description:
      "Intuitive step-by-step forms guide you through the application process.",
  },
  {
    icon: "lucide:file-up",
    title: "Easy Document Upload",
    description:
      "Securely upload and manage all required scholarship documents online.",
  },
  {
    icon: "lucide:eye",
    title: "Real-Time Status Tracking",
    description:
      "Monitor your application progress and receive timely updates.",
  },
  {
    icon: "lucide:bell",
    title: "Automated Notifications",
    description:
      "Stay informed about deadlines, status changes, and requirements.",
  },
  {
    icon: "lucide:layout-dashboard",
    title: "Personalized Dashboard",
    description: "Manage your profile, track payments, and communicate easily.",
  },
];

const adminFeatures = [
  {
    icon: "lucide:users",
    title: "Efficient Application Review",
    description:
      "Process applications in bulk with streamlined verification tools.",
  },
  {
    icon: "lucide:file-check-2",
    title: "Automated Eligibility Checks",
    description:
      "Quickly identify qualified candidates based on defined criteria.",
  },
  {
    icon: "lucide:dollar-sign",
    title: "Streamlined Financials",
    description:
      "Manage disbursements, track payment schedules, and generate reports.",
  },
  {
    icon: "lucide:sliders-horizontal",
    title: "Flexible Program Admin",
    description:
      "Configure eligibility rules, workflows, and program settings.",
  },
  {
    icon: "lucide:bar-chart-3",
    title: "Insightful Analytics",
    description:
      "Gain valuable insights into application trends and program performance.",
  },
];

// How it Works Steps
const processSteps = [
  {
    id: 1,
    title: "Register & Apply",
    description:
      "Students create an account and submit their application through the portal.",
    icon: "lucide:user-plus",
  },
  {
    id: 2,
    title: "Document Submission",
    description: "Upload required documents securely for verification.",
    icon: "lucide:file-up",
  },
  {
    id: 3,
    title: "Admin Review",
    description: "Administrators review applications and verify eligibility.",
    icon: "lucide:search-check",
  },
  {
    id: 4,
    title: "Award & Notification",
    description:
      "Successful applicants are notified and awarded the scholarship.",
    icon: "lucide:award",
  },
  {
    id: 5,
    title: "Track & Manage",
    description:
      "Students and admins track progress and manage scholarship details online.",
    icon: "lucide:list-checks",
  },
];

// FAQ Items based on README context
const faqItems = [
  {
    value: "item-1",
    title: "Who is PhilexScholar for?",
    content:
      "PhilexScholar is designed for students applying for scholarships offered by Philex Mines and the administrators managing these programs.",
  },
  {
    value: "item-2",
    title: "How do I apply for a scholarship?",
    content:
      "If you are eligible, you can register for an account and follow the step-by-step application process within the portal. Ensure you have all required documents ready for upload.",
  },
  {
    value: "item-3",
    title: "How can I track my application status?",
    content:
      "Once you submit your application, you can log in to your student dashboard to see the real-time status and receive notifications about any updates.",
  },
  {
    value: "item-4",
    title: "Is my data secure?",
    content:
      "Yes, we prioritize data security. PhilexScholar uses modern security practices and technologies like Laravel Sanctum to protect your personal information and documents.",
  },
];

// Tech Stack Logos (adjust as needed)
const techStack = [
  { icon: "logos:laravel", name: "Laravel" },
  { icon: "logos:vue", name: "Vue.js" },
  {
    icon: "simple-icons:inertia",
    name: "Inertia.js",
    color: "text-purple-500",
  },
  { icon: "logos:tailwindcss-icon", name: "Tailwind CSS" },
  { icon: "logos:sqlite", name: "SQLite" },
];
</script>

<template>
  <WebLayout
    :can-login="canLogin"
    :can-register="canRegister"
    :app-name="appName"
  >
    <!-- Hero Section -->
    <section class="relative border-b bg-background overflow-hidden">
      <!-- Enhanced Background -->
      <div
        aria-hidden="true"
        class="absolute inset-0 -z-10 opacity-50 dark:opacity-60"
      >
        <!-- Grid -->
        <div
          class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:36px_36px]"
        />
        <!-- Radial Gradient Mask -->
        <div
          class="absolute inset-0 bg-background [mask-image:radial-gradient(ellipse_50%_60%_at_50%_0%,#000_70%,transparent_110%)]"
        />
        <!-- Subtle Glow -->
        <div
          class="absolute left-1/2 top-0 -z-20 -translate-x-1/2 h-[450px] w-[700px] rounded-full bg-primary/10 blur-[120px]"
        />
      </div>

      <div
        class="container relative mx-auto px-4 py-32 sm:py-40 lg:py-48 text-center"
      >
        <!-- Optional Badge -->
        <div class="mb-6 inline-flex justify-center">
          <Badge
            variant="outline"
            class="rounded-full border bg-primary/10 px-4 py-1.5 text-sm font-medium"
          >
            <Icon icon="lucide:sparkles" class="mr-1.5 h-4 w-4 text-primary" />
            Now Accepting Applications!
          </Badge>
        </div>

        <!-- Main Heading - Emphasis on Typography -->
        <h1
          class="text-5xl font-extrabold tracking-tighter text-foreground sm:text-6xl md:text-7xl lg:text-8xl"
          :style="{ textWrap: 'balance' }"
        >
          Welcome to <span class="text-primary">PhilexScholar</span>
        </h1>
        <p
          class="mt-6 text-xl font-normal text-muted-foreground sm:text-2xl md:text-3xl"
          :style="{ textWrap: 'balance' }"
        >
          Your Digital Hub for Philex Mines Scholarships.
        </p>

        <!-- Subtitle - Clear but secondary -->
        <p
          class="mx-auto mt-8 max-w-3xl text-lg text-muted-foreground/80"
          :style="{ textWrap: 'pretty' }"
          fetchpriority="high"
        >
          Streamlining the entire scholarship process – from application and
          document submission to tracking and disbursement – all in one secure,
          modern platform.
        </p>

        <!-- CTA Buttons - Clear Action -->
        <div
          class="mt-12 flex flex-col items-center justify-center gap-4 sm:flex-row"
        >
          <Button
            v-if="canRegister"
            :as="Link"
            href="/register"
            size="xl"
            class="w-full sm:w-auto px-8 py-3"
          >
            <Icon icon="lucide:user-plus" class="mr-2 h-5 w-5" />
            Register to Apply
          </Button>
          <Button
            v-if="canLogin"
            :as="Link"
            href="/login"
            size="xl"
            variant="outline"
            class="w-full sm:w-auto px-8 py-3"
          >
            <Icon icon="lucide:log-in" class="mr-2 h-5 w-5" />
            Login to Dashboard
          </Button>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="container mx-auto px-4 py-24 sm:py-32">
      <div class="mx-auto max-w-3xl text-center mb-16">
        <h2
          class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl lg:text-5xl"
        >
          Empowering Futures, Simplified.
        </h2>
        <p class="mt-4 text-lg text-muted-foreground sm:text-xl">
          PhilexScholar offers dedicated features tailored for both students and
          administrators.
        </p>
      </div>

      <Tabs default-value="students" class="w-full max-w-5xl mx-auto">
        <TabsList class="grid w-full grid-cols-2 h-12">
          <TabsTrigger value="students" class="text-base h-full">
            <Icon icon="lucide:graduation-cap" class="mr-2 h-5 w-5" />
            For Students
          </TabsTrigger>
          <TabsTrigger value="administrators" class="text-base h-full">
            <Icon icon="lucide:user-cog" class="mr-2 h-5 w-5" />
            For Administrators
          </TabsTrigger>
        </TabsList>

        <TabsContent value="students" class="mt-10">
          <div
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8"
          >
            <Card
              v-for="feature in studentFeatures"
              :key="feature.title"
              class="group relative overflow-hidden border-border/50 hover:border-primary/50 transition-colors duration-300 bg-card"
            >
              <!-- Optional subtle gradient border effect on hover -->
              <!-- <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-br from-primary/10 via-transparent to-transparent"></div> -->

              <CardHeader class="relative z-10 pb-4">
                <div
                  class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 border border-primary/20 text-primary"
                >
                  <Icon :icon="feature.icon" class="h-6 w-6" />
                </div>
                <CardTitle class="text-lg font-semibold tracking-tight">
                  {{ feature.title }}
                </CardTitle>
              </CardHeader>
              <CardContent class="relative z-10">
                <p class="text-sm text-muted-foreground">
                  {{ feature.description }}
                </p>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <TabsContent value="administrators" class="mt-10">
          <div
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8"
          >
            <Card
              v-for="feature in adminFeatures"
              :key="feature.title"
              class="group relative overflow-hidden border-border/50 hover:border-primary/50 transition-colors duration-300 bg-card"
            >
              <CardHeader class="relative z-10 pb-4">
                <div
                  class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 border border-primary/20 text-primary"
                >
                  <Icon :icon="feature.icon" class="h-6 w-6" />
                </div>
                <CardTitle class="text-lg font-semibold tracking-tight">
                  {{ feature.title }}
                </CardTitle>
              </CardHeader>
              <CardContent class="relative z-10">
                <p class="text-sm text-muted-foreground">
                  {{ feature.description }}
                </p>
              </CardContent>
            </Card>
          </div>
        </TabsContent>
      </Tabs>
    </section>

    <!-- How it Works Section -->
    <section
      id="how-it-works"
      class="border-t bg-gradient-to-b from-background to-muted/30 py-24 sm:py-32"
    >
      <div class="container mx-auto px-4">
        <div class="mx-auto max-w-3xl text-center mb-16">
          <h2
            class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl lg:text-5xl"
          >
            Simple Steps to Success
          </h2>
          <p class="mt-4 text-lg text-muted-foreground sm:text-xl">
            Follow our straightforward process for scholarship application and
            management.
          </p>
        </div>

        <div class="relative max-w-5xl mx-auto">
          <!-- Connecting Line -->
          <div
            aria-hidden="true"
            class="absolute left-0 right-0 top-6 h-1 bg-border rounded-full md:left-6 md:right-6"
          ></div>

          <!-- Steps Grid -->
          <div class="relative grid grid-cols-1 gap-12 md:grid-cols-5 md:gap-8">
            <div
              v-for="(step, index) in processSteps"
              :key="step.id"
              class="flex flex-col items-center text-center md:items-start md:text-left"
            >
              <!-- Step Node (Circle + Icon) -->
              <div
                class="relative z-10 flex h-12 w-12 items-center justify-center rounded-full border-2 border-primary bg-background text-primary mb-4 ring-4 ring-background"
              >
                <!-- <span class="font-bold text-lg">{{ index + 1 }}</span> -->
                <Icon :icon="step.icon" class="h-6 w-6" />
              </div>

              <!-- Step Content -->
              <div class="mt-2">
                <h3 class="text-lg font-semibold text-foreground">
                  {{ step.title }}
                </h3>
                <p class="mt-1 text-sm text-muted-foreground">
                  {{ step.description }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Technology Section -->
    <section id="tech-stack" class="border-t py-16 sm:py-24">
      <div class="container mx-auto px-4">
        <div class="text-center">
          <h2
            class="text-base font-semibold uppercase tracking-wider text-primary"
          >
            Built with Reliable Technology
          </h2>
          <p
            class="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl"
          >
            Powered by the VILT Stack
          </p>
          <p class="mx-auto mt-4 max-w-2xl text-lg text-muted-foreground">
            Leveraging modern tools for a robust, secure, and user-friendly
            experience.
          </p>
        </div>
        <div class="mt-12">
          <div
            class="flex flex-wrap items-center justify-center gap-8 sm:gap-10 md:gap-14"
          >
            <div
              v-for="tech in techStack"
              :key="tech.name"
              class="flex flex-col items-center gap-2 text-center"
            >
              <Icon
                :icon="tech.icon"
                class="h-10 w-10 opacity-75 transition-opacity hover:opacity-100"
                :class="tech.color || 'text-muted-foreground'"
              />
              <span class="text-xs font-medium text-muted-foreground">{{
                tech.name
              }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="border-t bg-muted/40 py-24 sm:py-32">
      <div class="container mx-auto px-4">
        <div class="mx-auto max-w-3xl text-center mb-16">
          <h2
            class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl lg:text-5xl"
          >
            Have Questions?
          </h2>
          <p class="mt-4 text-lg text-muted-foreground sm:text-xl">
            Find answers to common inquiries about the PhilexScholar program.
          </p>
        </div>
        <div class="mx-auto max-w-3xl">
          <!-- Using Accordion for FAQ -->
          <Accordion
            type="single"
            class="w-full space-y-4"
            collapsible
            default-value="item-1"
          >
            <AccordionItem
              v-for="(item, index) in faqItems"
              :key="item.value"
              :value="item.value"
              class="border rounded-lg bg-background shadow-sm transition-shadow hover:shadow-md overflow-hidden"
            >
              <AccordionTrigger
                class="flex w-full items-center justify-between p-5 text-lg font-medium text-left hover:no-underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 data-[state=open]:bg-muted/50"
                :aria-controls="`faq-content-${index}`"
                :id="`faq-trigger-${index}`"
              >
                <span>{{ item.title }}</span>
                <!-- Icon indicates state -->
                <Icon
                  icon="lucide:chevron-down"
                  class="h-5 w-5 shrink-0 text-muted-foreground transition-transform duration-200 group-data-[state=open]:rotate-180"
                />
                <!-- Using Lucide component directly:
                    <ChevronDown class="h-5 w-5 shrink-0 text-muted-foreground transition-transform duration-200 group-data-[state=open]:rotate-180" />
                    -->
              </AccordionTrigger>
              <AccordionContent
                class="px-5 pb-5 pt-0 text-base text-muted-foreground data-[state=closed]:animate-accordion-up data-[state=open]:animate-accordion-down"
                :id="`faq-content-${index}`"
                :aria-labelledby="`faq-trigger-${index}`"
                role="region"
              >
                {{ item.content }}
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </div>
        <div class="mt-12 text-center">
          <p class="text-muted-foreground">
            Can't find your answer?
            <a
              :href="supportEmail"
              class="font-medium text-primary underline-offset-4 hover:underline"
              >Contact Support</a
            >.
          </p>
        </div>
      </div>
    </section>
    <section
      class="relative border-t bg-background py-24 sm:py-32 overflow-hidden"
    >
      <!-- Background Elements -->
      <div
        aria-hidden="true"
        class="absolute inset-0 -z-10 opacity-40 dark:opacity-50"
      >
        <div
          class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:24px_24px]"
        />
        <div
          class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 h-[600px] w-[120%] rounded-full bg-gradient-radial from-primary/10 via-primary/5 to-transparent blur-[80px]"
        />
      </div>

      <div class="container relative mx-auto px-4 z-10">
        <div class="mx-auto max-w-3xl text-center">
          <h2
            class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl lg:text-6xl"
          >
            Ready to Start Your Journey?
          </h2>
          <p
            class="mt-6 text-lg text-muted-foreground sm:text-xl"
            :style="{ textWrap: 'pretty' }"
          >
            Join the community of Philex scholars. Register today to begin your
            application or log in to manage your scholarship details with ease.
            Secure your future, powered by PhilexScholar.
          </p>
          <div
            class="mt-12 flex flex-col items-center justify-center gap-4 sm:flex-row"
          >
            <Button
              v-if="canRegister"
              :as="Link"
              href="/register"
              size="xl"
              class="w-full sm:w-auto px-10 py-3.5"
            >
              <Icon icon="lucide:user-plus" class="mr-2 h-5 w-5" />
              Register Now
            </Button>
            <Button
              v-if="canLogin"
              :as="Link"
              href="/login"
              size="xl"
              variant="outline"
              class="w-full sm:w-auto px-10 py-3.5"
            >
              <Icon icon="lucide:log-in" class="mr-2 h-5 w-5" />
              Login to Dashboard
            </Button>
          </div>
        </div>
      </div>
    </section>
  </WebLayout>
</template>
