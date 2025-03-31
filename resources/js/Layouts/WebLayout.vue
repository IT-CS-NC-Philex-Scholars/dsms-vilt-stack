<script setup>
import Button from "@/Components/shadcn/ui/button/Button.vue";
import { Icon } from "@iconify/vue";
import { Link, usePage } from "@inertiajs/vue3";
import { useColorMode } from "@vueuse/core";
import { ref, computed, watch } from "vue";
// import { useRoute } from "vue-router"; // Import computed
defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
});

const page = usePage(); // Use usePage hook
watch(
  () => page.url,
  () => {
    isMenuOpen.value = false;
  },
);
const appName = computed(() => page.props.appName || "PhilexScholar"); // Get app name from props

const mode = useColorMode({
  attribute: "class",
  modes: {
    light: "",
    dark: "dark",
  },
});

// Update navLinks for PhilexScholar
const navLinks = [
  { label: "Features", href: "#features", external: false },
  { label: "How it Works", href: "#how-it-works", external: false },
  { label: "FAQ", href: "#faq", external: false },
  // Add link to external docs if available, otherwise remove or keep internal link
  // { label: 'Docs', href: 'https://docs.philexscholar.com', external: true },
];

// Update relevant external links if needed, otherwise remove or use placeholders
const companyWebsiteUrl = "https://philexmining.com.ph"; // Example Philex Mines website
const supportEmail = "mailto:support@philexscholar.com"; // Example support email from Readme

const isMenuOpen = ref(false);

function toggleMenu() {
  isMenuOpen.value = !isMenuOpen.value;
}

// Define toggleMobileMenu if it was missing
function toggleMobileMenu() {
  isMenuOpen.value = false;
}
function scrollToSection(event, href) {
  if (href.startsWith("#")) {
    event.preventDefault();
    const targetId = href.substring(1);
    const targetElement = document.getElementById(targetId);
    if (targetElement) {
      // Close mobile menu if open
      isMenuOpen.value = false;
      // Smooth scroll
      targetElement.scrollIntoView({ behavior: "smooth", block: "start" });
      // Optional: Update URL hash without page reload after scrolling
      // history.pushState(null, null, href);
    } else {
      // Fallback for external links or if element not found
      window.location.href = href;
    }
  } else {
    // Allow default behavior for non-hash links (like external URLs)
    // Close mobile menu if navigating away
    isMenuOpen.value = false;
  }
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-background text-foreground">
    <header
      class="sticky top-0 z-50 w-full border-b border-border/40 bg-background/90 backdrop-blur-lg"
    >
      <div class="container flex h-16 items-center justify-between sm:h-20">
        <!-- Logo & App Name -->
        <div class="flex items-center">
          <Link
            class="flex items-center space-x-2 mr-6"
            href="/"
            :aria-label="appName"
          >
            <Icon
              icon="lucide:graduation-cap"
              class="h-7 w-7 text-primary sm:h-8 sm:w-8"
              aria-hidden="true"
            />
            <span class="hidden font-bold text-lg sm:inline-block">
              {{ appName }}
            </span>
          </Link>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-6 text-sm font-medium">
          <a
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            @click="scrollToSection($event, link.href)"
            class="relative transition-colors hover:text-primary py-2 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-primary after:transition-all after:duration-300 hover:after:w-full"
            :class="[
              link.external ? 'text-muted-foreground' : 'text-foreground/80',
            ]"
            :target="link.external ? '_blank' : undefined"
            :rel="link.external ? 'noopener noreferrer' : undefined"
          >
            {{ link.label }}
          </a>
        </nav>

        <!-- Actions & Mobile Toggle -->
        <div class="flex items-center space-x-2 sm:space-x-3">
          <!-- Desktop Auth Buttons -->
          <div class="hidden sm:flex items-center space-x-2">
            <template v-if="!$page.props.auth.user">
              <Button
                v-if="canLogin"
                variant="ghost"
                size="sm"
                :as="Link"
                href="/login"
              >
                Login
              </Button>
              <Button
                v-if="canRegister"
                variant="default"
                size="sm"
                :as="Link"
                href="/register"
              >
                Register
              </Button>
            </template>
            <Button
              v-else
              variant="outline"
              size="sm"
              :as="Link"
              href="/dashboard"
            >
              Dashboard
            </Button>
          </div>

          <!-- Theme Toggle -->
          <Button
            variant="ghost"
            size="icon"
            aria-label="Toggle Theme"
            class="h-9 w-9 sm:h-10 sm:w-10"
            @click="mode = mode === 'dark' ? 'light' : 'dark'"
          >
            <Icon
              class="text-muted-foreground h-5 w-5"
              :icon="mode === 'dark' ? 'lucide:sun' : 'lucide:moon'"
            />
          </Button>

          <!-- Mobile Menu Button -->
          <Button
            class="md:hidden h-9 w-9 sm:h-10 sm:w-10"
            variant="ghost"
            size="icon"
            aria-label="Toggle menu"
            @click="toggleMenu"
          >
            <Icon
              :icon="isMenuOpen ? 'lucide:x' : 'lucide:menu'"
              class="h-6 w-6"
              aria-hidden="true"
            />
          </Button>
        </div>
      </div>

      <!-- Mobile Menu Overlay -->
      <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 translate-y-[-10px]"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-[-10px]"
      >
        <div
          v-show="isMenuOpen"
          class="absolute top-full left-0 w-full border-t border-border/40 bg-background shadow-lg md:hidden"
        >
          <nav class="flex flex-col p-4 space-y-2">
            <a
              v-for="link in navLinks"
              :key="link.href"
              :href="link.href"
              @click="scrollToSection($event, link.href)"
              class="block rounded-md px-3 py-2 text-base font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
              :class="[
                link.external ? 'text-muted-foreground' : 'text-foreground',
              ]"
              :target="link.external ? '_blank' : undefined"
              :rel="link.external ? 'noopener noreferrer' : undefined"
            >
              {{ link.label }}
            </a>
            <hr class="border-border/40 my-2" />
            <!-- Mobile Auth Buttons -->
            <template v-if="!$page.props.auth.user">
              <Button
                v-if="canLogin"
                variant="outline"
                :as="Link"
                href="/login"
                class="w-full justify-start"
              >
                Login
              </Button>
              <Button
                v-if="canRegister"
                variant="default"
                :as="Link"
                href="/register"
                class="w-full justify-start"
              >
                Register
              </Button>
            </template>
            <Button
              v-else
              variant="outline"
              :as="Link"
              href="/dashboard"
              class="w-full justify-start"
            >
              Dashboard
            </Button>
          </nav>
        </div>
      </transition>
    </header>

    <main class="flex-grow">
      <slot />
    </main>

    <footer class="border-t border-border/40 bg-muted/40">
      <div class="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
          <!-- Branding & Copyright -->
          <div
            class="flex flex-col items-center text-center lg:items-start lg:text-left"
          >
            <Link
              class="flex items-center space-x-2 mb-4"
              href="/"
              :aria-label="appName"
            >
              <Icon
                icon="lucide:graduation-cap"
                class="h-7 w-7 text-primary"
                aria-hidden="true"
              />
              <span class="font-bold text-lg">
                {{ appName }}
              </span>
            </Link>
            <p class="text-sm text-muted-foreground max-w-xs">
              Transforming scholarship management for the Philex Mines
              community.
            </p>
            <p class="mt-4 text-xs text-muted-foreground/80">
              © {{ new Date().getFullYear() }} Philex Mining Corp. All rights
              reserved.
            </p>
          </div>

          <!-- Navigation Links -->
          <div class="flex flex-col items-center lg:items-start">
            <h3
              class="text-sm font-semibold uppercase tracking-wider text-foreground mb-4"
            >
              Navigate
            </h3>
            <nav
              class="flex flex-col space-y-2 items-center text-center lg:items-start lg:text-left"
            >
              <a
                v-for="link in navLinks.filter((l) => !l.external)"
                :key="link.href"
                :href="link.href"
                @click="scrollToSection($event, link.href)"
                class="text-sm text-muted-foreground transition-colors hover:text-primary"
              >
                {{ link.label }}
              </a>
              <!-- Add other relevant links like Privacy Policy, Terms -->
              <!--
                          <a href="/privacy" class="text-sm text-muted-foreground transition-colors hover:text-primary">Privacy Policy</a>
                          <a href="/terms" class="text-sm text-muted-foreground transition-colors hover:text-primary">Terms of Service</a>
                          -->
            </nav>
          </div>

          <!-- Contact & Socials -->
          <div
            class="flex flex-col items-center text-center lg:items-start lg:text-left"
          >
            <h3
              class="text-sm font-semibold uppercase tracking-wider text-foreground mb-4"
            >
              Connect
            </h3>
            <div class="flex flex-col space-y-3">
              <a
                :href="supportEmail"
                class="flex items-center gap-2 text-sm text-muted-foreground transition-colors hover:text-primary"
              >
                <Icon icon="lucide:mail" class="h-4 w-4" />
                <span>Contact Support</span>
              </a>
              <a
                :href="companyWebsiteUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2 text-sm text-muted-foreground transition-colors hover:text-primary"
                aria-label="Philex Mining Corp Website"
                title="Philex Mining Corp Website"
              >
                <Icon icon="lucide:building" class="h-4 w-4" />
                <span>Philex Mining Corp.</span>
              </a>
            </div>
            <!-- Social Icons -->
            <!-- Add social links if applicable
                   <div class="flex gap-4 mt-6 justify-center lg:justify-start">
                       <a href="#" target="_blank" rel="noopener noreferrer" class="text-muted-foreground hover:text-primary" aria-label="Facebook">
                          <Icon icon="lucide:facebook" class="h-5 w-5" />
                       </a>
                        <a href="#" target="_blank" rel="noopener noreferrer" class="text-muted-foreground hover:text-primary" aria-label="Twitter">
                          <Icon icon="lucide:twitter" class="h-5 w-5" />
                       </a>
                   </div>
                   -->
          </div>
        </div>

        <!-- Optional: Bottom bar for simple text -->
        <!--
           <div class="mt-12 border-t border-border/40 pt-8 text-center">
               <p class="text-xs text-muted-foreground">Built with ❤️ by the Philex Mines Technology Team.</p>
           </div>
           -->
      </div>
    </footer>
  </div>
</template>
