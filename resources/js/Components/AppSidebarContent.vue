<script setup>
import {
  SidebarContent,
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/Components/shadcn/ui/sidebar";
import { Icon } from "@iconify/vue";
import { Link, usePage } from "@inertiajs/vue3"; // Import usePage
import { useColorMode } from "@vueuse/core";
import { computed, inject } from "vue";

const route = inject("route");
const page = usePage(); // Get page instance
const mode = useColorMode({
  attribute: "class",
  modes: { light: "", dark: "dark" },
});

// Scholar-Centric Navigation Configuration
const navigationConfig = computed(() => [
  {
    label: "Navigation", // Main navigation group
    items: [
      {
        name: "Dashboard",
        icon: "lucide:layout-dashboard",
        route: "dashboard", // Keep dashboard as home/overview
      },
      {
        name: "My Application",
        icon: "lucide:file-text", // Icon for application document/details
        // Use a specific route for the user's main/current application if applicable
        // Or link to an index page if they can have multiple
        route: "applications.index", // Suggestion: Route to view application(s)
      },
      {
        name: "Find Scholarships",
        icon: "lucide:search", // Icon for searching/discovery
        route: "scholarships.index", // Suggestion: Route to browse available scholarships
      },
    ],
  },
  {
    label: "Account", // Account management group
    items: [
      {
        name: "Notifications",
        icon: "lucide:bell", // Icon for notifications
        route: "notifications.index", // Suggestion: Route to view notifications
        // Optional: Add badge for count if available
        badgeCount: page.props.auth.user?.unread_notifications_count, // Example prop
      },
      {
        name: "My Documents",
        icon: "lucide:folder-open", // Icon for managing documents
        route: "documents.index", // Suggestion: Route for document management
      },
      {
        name: "My Awards", // Or "Financials"
        icon: "lucide:award", // Or "lucide:banknote"
        route: "awards.index", // Suggestion: Route for viewing awarded scholarships/payments
        // Optional: Conditionally show this based on user status/awards
        // condition: () => page.props.auth.user?.has_awards === true
      },
      {
        name: "Profile Settings",
        icon: "lucide:settings",
        route: "profile.show", // Existing Jetstream/Breeze profile route
      },
    ],
  },
  {
    label: "Support", // Support and Help group
    class: "mt-auto pt-4 border-t border-border", // Push to bottom with separator
    items: [
      {
        name: "Help Center", // Renamed from Documentation
        icon: "lucide:help-circle", // More appropriate icon
        href: "#", // Replace with your actual Help Center URL
        // href: "https://docs.yourapp.com",
        external: true,
      },
      {
        name: "Contact Support",
        icon: "lucide:life-buoy",
        href: "#", // Replace with your support link/mailto
        // href: "mailto:support@yourapp.com",
        external: true,
      },
    ],
  },
]);

const isDarkMode = computed(() => mode.value === "dark");

// Keep renderLink function, handles internal/external links
function renderLink(item) {
  if (item.external) {
    return {
      is: "a",
      href: item.href, // Directly use href for external
      target: "_blank",
      rel: "noopener noreferrer", // Added for security
    };
  }
  // Check if route exists before trying to generate it
  if (item.route && typeof route === "function" && route().has(item.route)) {
    return {
      is: Link,
      href: route(item.route),
    };
  }
  // Fallback if route doesn't exist (useful during development)
  console.warn(`Route [${item.route}] does not exist.`);
  return {
    is: "span", // Render as a non-clickable span or div
    class: "cursor-not-allowed opacity-50",
  };
}

// Helper to check if an item should be displayed (e.g., based on condition)
function shouldDisplay(item) {
  if (item.condition && typeof item.condition === "function") {
    return item.condition();
  }
  return true; // Display by default
}
</script>

<template>
  <SidebarContent>
    <SidebarGroup
      v-for="(group, index) in navigationConfig"
      :key="group.label || `group-${index}`"
      :class="group.class"
    >
      <SidebarGroupLabel v-if="group.label">
        {{ group.label }}
      </SidebarGroupLabel>
      <SidebarMenu>
        <!-- Iterate over items within the group -->
        <template v-for="item in group.items" :key="item.name">
          <SidebarMenuItem
            v-if="shouldDisplay(item)"
            :class="{
              'bg-secondary text-secondary-foreground dark:bg-sidebar-accent dark:text-sidebar-accent-foreground rounded font-semibold':
                !item.external &&
                item.route &&
                typeof route === 'function' &&
                route().current(item.route),
            }"
          >
            <SidebarMenuButton as-child>
              <component
                v-bind="renderLink(item)"
                :is="renderLink(item).is"
                class="flex items-center justify-between w-full"
              >
                <div class="flex items-center gap-2">
                  <Icon :icon="item.icon" class="h-4 w-4" />
                  <span>{{ item.name }}</span>
                </div>
                <!-- Optional Badge for Counts (e.g., notifications) -->
                <Badge
                  v-if="item.badgeCount > 0"
                  variant="destructive"
                  class="h-5 px-1.5 text-xs ml-auto"
                >
                  {{ item.badgeCount }}
                </Badge>
              </component>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </template>

        <!-- Theme Toggle - Placed after the last group's items -->
        <SidebarMenuItem v-if="index === navigationConfig.length - 1">
          <SidebarMenuButton
            @click="mode = isDarkMode ? 'light' : 'dark'"
            class="gap-2"
          >
            <Icon
              :icon="isDarkMode ? 'lucide:moon' : 'lucide:sun'"
              class="h-4 w-4"
            />
            <span>{{ isDarkMode ? "Dark" : "Light" }} Mode</span>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarGroup>
  </SidebarContent>
</template>
