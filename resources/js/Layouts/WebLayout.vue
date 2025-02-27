<script setup>
import Button from "@/Components/shadcn/ui/button/Button.vue";
import Badge from "@/Components/shadcn/ui/badge/Badge.vue";
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";
import { useColorMode } from "@vueuse/core";
import { ref } from "vue";
import Sonner from '@/Components/shadcn/ui/sonner/Sonner.vue'
defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
});

const mode = useColorMode({
  attribute: "class",
  modes: {
    light: "",
    dark: "dark",
  },
});

const navLinks = [
  {
    label: "Features",
    href: "/#features",
    icon: "lucide:layout-grid",
    external: false,
  },
  { label: "FAQ", href: "/#faq", icon: "lucide:help-circle", external: false },
  {
    label: "How to Apply",
    href: "/#how-to-apply",
    icon: "lucide:file-text",
    external: false,
  },
];

const isMenuOpen = ref(false);

function toggleMenu() {
  isMenuOpen.value = !isMenuOpen.value;
}
</script>

<template>
    <Sonner position="top-center" />
  <div class="min-h-screen">
    <header
      class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur-sm"
    >
      <div class="container flex h-20 items-center justify-between">
        <div class="flex items-center gap-8">
          <a
            class="flex items-center gap-3"
            href="/"
            :aria-label="$page.props.name"
          >
            <Icon
              icon="lucide:graduation-cap"
              class="h-8 w-8 text-primary"
              aria-hidden="true"
            />
            <div class="hidden flex-col sm:flex">
              <span class="text-lg font-bold">ScholarTrack</span>
              <Badge variant="outline" class="text-xs"
                >Scholarship Portal</Badge
              >
            </div>
          </a>

          <nav class="hidden md:flex items-center gap-6">
            <a
              v-for="link in navLinks"
              :key="link.href"
              :href="link.href"
              class="flex items-center gap-2 text-sm font-medium transition-colors hover:text-primary"
            >
              <Icon :icon="link.icon" class="h-4 w-4" />
              {{ link.label }}
            </a>
          </nav>
        </div>

        <div class="flex items-center gap-4">
          <div class="hidden sm:flex gap-3">
            <template v-if="!$page.props.auth.user">
              <Button
                variant="outline"
                :as="Link"
                href="/login"
                class="flex gap-2"
              >
                <Icon icon="lucide:log-in" class="h-4 w-4" />
                Login
              </Button>
              <Button :as="Link" href="/pre-qualification" class="flex gap-2">
                <Icon icon="lucide:file-plus" class="h-4 w-4" />
                Apply Now
              </Button>
            </template>
            <Button
              v-else
              variant="outline"
              :as="Link"
              href="/dashboard"
              class="flex gap-2"
            >
              <Icon icon="lucide:layout-dashboard" class="h-4 w-4" />
              Dashboard
            </Button>
          </div>

          <Button
            variant="ghost"
            size="icon"
            aria-label="Toggle Theme"
            @click="mode = mode === 'dark' ? 'light' : 'dark'"
          >
            <Icon
              class="h-5 w-5"
              :icon="mode === 'dark' ? 'lucide:sun' : 'lucide:moon'"
            />
          </Button>

          <Button
            class="md:hidden"
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

      <!-- Mobile menu -->
      <div
        v-show="isMenuOpen"
        class="md:hidden border-t bg-background/95 backdrop-blur-sm"
      >
        <nav class="container flex flex-col py-4 gap-3">
          <a
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-accent"
            @click="toggleMenu"
          >
            <Icon :icon="link.icon" class="h-5 w-5" />
            {{ link.label }}
          </a>

          <div class="border-t my-2"></div>

          <template v-if="!$page.props.auth.user">
            <Button
              variant="outline"
              :as="Link"
              href="/login"
              class="w-full justify-start gap-3"
            >
              <Icon icon="lucide:log-in" class="h-5 w-5" />
              Login
            </Button>
            <Button
              :as="Link"
              href="/apply-now"
              class="w-full justify-start gap-3"
            >
              <Icon icon="lucide:file-plus" class="h-5 w-5" />
              Apply Now
            </Button>
          </template>
          <Button
            v-else
            variant="outline"
            :as="Link"
            href="/dashboard"
            class="w-full justify-start gap-3"
          >
            <Icon icon="lucide:layout-dashboard" class="h-5 w-5" />
            Dashboard
          </Button>
        </nav>
      </div>
    </header>

    <slot />

    <!-- Footer -->
    <footer class="border-t bg-background/95">
      <div class="container py-8">
        <div
          class="flex flex-col items-center justify-between gap-4 sm:flex-row"
        >
          <div class="flex items-center gap-3">
            <Icon icon="lucide:graduation-cap" class="h-6 w-6 text-primary" />
            <span class="text-sm font-medium"
              >© 2024 ScholarTrack. All rights reserved.</span
            >
          </div>
          <div class="flex gap-4">
            <Button
              variant="ghost"
              size="icon"
              aria-label="Toggle Theme"
              @click="mode = mode === 'dark' ? 'light' : 'dark'"
            >
              <Icon
                class="h-5 w-5"
                :icon="mode === 'dark' ? 'lucide:sun' : 'lucide:moon'"
              />
            </Button>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
