import { useSeoMeta } from '@unhead/vue'

// Default SEO meta tags
const defaultSeoMeta = {
  title: 'Home',
  titleTemplate: '%s | PhilexScholar - Digital Scholarship Management Hub',
  description: 'PhilexScholar is a centralized platform for managing scholarships in the Philex Mines community, streamlining the entire scholarship lifecycle from application to disbursement.',
  keywords: 'PhilexScholar, Philex Mines, scholarship management, digital scholarship, student portal, scholarship application, VILT stack, Laravel, Vue, Inertia, TailwindCSS',
  robots: 'index, follow',
  themeColor: '#000000',

  // Open Graph
  ogTitle: '%s | PhilexScholar - Digital Scholarship Management Hub',
  ogDescription: 'PhilexScholar addresses the challenges of traditional scholarship management by providing a centralized, efficient, and user-friendly platform for the Philex Mines community.',
  ogUrl: window.location.origin,
  ogType: 'website',
  ogImage: `${window.location.origin}/images/dashboard-image.png`,
  ogSiteName: 'PhilexScholar',
  ogLocale: 'en_US',

  // Twitter
  twitterTitle: '%s | PhilexScholar - Digital Scholarship Management Hub',
  twitterDescription: 'PhilexScholar addresses the challenges of traditional scholarship management by providing a centralized, efficient, and user-friendly platform for the Philex Mines community.',
  twitterCard: 'summary_large_image',
  twitterImage: `${window.location.origin}/images/dashboard-image.png`,
  twitterSite: '@philexscholar',
}

/**
 * Composable for managing SEO meta tags
 * @param {object|null} seoMeta - Custom SEO meta tags to apply
 * @param {object} options - Configuration options
 * @param {boolean} options.merge - When true, merges custom meta tags with defaults.
 *                                 When false, only uses custom meta tags.
 *                                 Useful for pages that need completely custom SEO
 *                                 without inheriting defaults.
 * @returns {void}
 */
export function useSeoMetaTags(seoMeta, options = { merge: true }) {
  if (!seoMeta)
    return useSeoMeta(defaultSeoMeta)

  return useSeoMeta(
    options.merge
      ? { ...defaultSeoMeta, ...seoMeta }
      : seoMeta,
  )
}
