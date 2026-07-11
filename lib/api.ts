import type { MediaArticleCategory } from "@/lib/media";

const DEFAULT_API_URL = "http://localhost:8000";
const DEFAULT_REVALIDATE_SECONDS = 300;

/** Next.js extends `fetch` with ISR options — not in standard `RequestInit`. */
type ApiFetchInit = RequestInit & {
  next?: { revalidate?: number | false };
};

export function getApiUrl(): string {
  const url = process.env.NEXT_PUBLIC_API_URL;
  return url?.replace(/\/$/, "") ?? DEFAULT_API_URL;
}

function apiFetchInit(locale: string, revalidate = DEFAULT_REVALIDATE_SECONDS): ApiFetchInit {
  return {
    headers: apiHeaders(locale),
    next: { revalidate },
  };
}

async function apiGet<T>(path: string, locale: string): Promise<T | null> {
  try {
    const response = await fetch(`${getApiUrl()}${path}`, apiFetchInit(locale));

    if (!response.ok) {
      return null;
    }

    return (await response.json()) as T;
  } catch {
    return null;
  }
}

async function apiPost<T>(
  path: string,
  locale: string,
  body: unknown,
): Promise<T> {
  const response = await fetch(`${getApiUrl()}${path}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      "Accept-Language": locale,
    },
    body: JSON.stringify(body),
  });

  const data = (await response.json()) as {
    message?: string;
    errors?: Record<string, string[]>;
  };

  if (!response.ok) {
    const firstError = data.errors
      ? Object.values(data.errors)[0]?.[0]
      : undefined;
    throw new Error(firstError ?? data.message ?? "Request failed");
  }

  return data as T;
}

export type MemberCityStat = {
  key: string;
  label: string;
  value: number;
  unit: string;
};

export type MemberCitiesPayload = {
  stats: MemberCityStat[];
  countriesGeoJson: GeoJsonFeatureCollection;
  citiesGeoJson: GeoJsonFeatureCollection;
};

export type GeoJsonFeatureCollection = {
  type: "FeatureCollection";
  features: Array<{
    type: "Feature";
    properties?: Record<string, unknown>;
    geometry: { type: string; coordinates: unknown };
    ccode?: string;
  }>;
};

function apiHeaders(locale: string): HeadersInit {
  return {
    Accept: "application/json",
    "Accept-Language": locale,
  };
}

export type HomePayload = {
  slider: { title: string; imageUrl: string | null }[];
  aboutIntro: {
    title: string;
    description: string;
    cta: string;
    mission: { title: string; description: string; readMore: string };
    vision: { title: string; description: string; readMore: string };
  };
  stats: {
    title: string;
    subtitle: string;
    items: { value: string; label: string; description: string }[];
  };
  memberCities: {
    title: string;
    stats: MemberCityStat[];
  };
  programs: {
    title: string;
    cta: string;
    items: { slug?: string; title: string; description: string; href: string }[];
  };
  mediaCenter: {
    title: string;
    subtitle: string;
    readMore: string;
    viewAll: string;
    featured: Record<string, unknown>[];
    items: Record<string, unknown>[];
  };
  knowledgeCenter: {
    viewIssue: string;
    downloadPdf: string;
    categories: {
      id: number;
      slug: string;
      title: string;
      description: string;
      items: {
        slug: string;
        title: string;
        date: string;
        href: string;
        pdfHref: string;
        image?: string;
      }[];
    }[];
    headerSlides: { id?: number; slug?: string; title: string; description: string }[];
    items: {
      slug: string;
      title: string;
      date: string;
      href: string;
      pdfHref: string;
      image?: string;
    }[];
  };
  membershipContact: {
    membership: { title: string; subtitle: string; cta: string; href: string };
    contact: {
      title: string;
      addressTitle: string;
      address: string;
      mapTitle: string;
      mapEmbedUrl: string;
      items: { label: string; value: string; type: string; href?: string }[];
    };
  };
};

export async function fetchHome(locale: string): Promise<HomePayload | null> {
  return apiGet<HomePayload>("/api/v1/home", locale);
}

export async function fetchMemberCities(
  locale: string,
): Promise<MemberCitiesPayload | null> {
  return apiGet<MemberCitiesPayload>("/api/v1/home/member-cities", locale);
}

export async function fetchMemberCityStats(
  locale: string,
): Promise<MemberCityStat[] | null> {
  const payload = await fetchMemberCities(locale);

  return payload?.stats ?? null;
}

export function memberCitiesGeoJsonUrls(): {
  countries: string;
  cities: string;
} {
  const base = `${getApiUrl()}/api/v1/home/member-cities`;

  return {
    countries: `${base}/countries.geojson`,
    cities: `${base}/cities.geojson`,
  };
}

export async function fetchMemberCitiesGeoJson(
  locale: string,
): Promise<{ countries: GeoJsonFeatureCollection; cities: GeoJsonFeatureCollection } | null> {
  const urls = memberCitiesGeoJsonUrls();

  try {
    const init = apiFetchInit(locale);
    const [countriesRes, citiesRes] = await Promise.all([
      fetch(urls.countries, init),
      fetch(urls.cities, init),
    ]);

    if (!countriesRes.ok || !citiesRes.ok) {
      return null;
    }

    return {
      countries: (await countriesRes.json()) as GeoJsonFeatureCollection,
      cities: (await citiesRes.json()) as GeoJsonFeatureCollection,
    };
  } catch {
    return null;
  }
}

export type ApiMediaCategory =
  | "news"
  | "newsletter"
  | "city-meetings"
  | "secretary-speaks";

export type PaginatedMeta = {
  page: number;
  limit: number;
  total: number;
  totalPages: number;
};

export function toApiMediaCategory(category: MediaArticleCategory): ApiMediaCategory {
  if (category === "cityMeetings") return "city-meetings";
  if (category === "secretarySpeaks") return "secretary-speaks";
  return category;
}

export async function fetchMediaArticles(
  category: MediaArticleCategory,
  locale: string,
  params: { page?: number; limit?: number; search?: string; year?: number; month?: number } = {},
): Promise<{ items: Record<string, unknown>[]; meta: PaginatedMeta } | null> {
  const apiCategory = toApiMediaCategory(category);
  const query = new URLSearchParams();

  if (params.page) query.set("page", String(params.page));
  if (params.limit) query.set("limit", String(params.limit));
  if (params.search) query.set("search", params.search);
  if (params.year) query.set("year", String(params.year));
  if (params.month) query.set("month", String(params.month));

  const qs = query.toString();
  const url = `${getApiUrl()}/api/v1/media/${apiCategory}${qs ? `?${qs}` : ""}`;

  try {
    const response = await fetch(url, apiFetchInit(locale));

    if (!response.ok) {
      return null;
    }

    const data = (await response.json()) as {
      data: Record<string, unknown>[];
      meta: PaginatedMeta;
    };

    return { items: data.data, meta: data.meta };
  } catch {
    return null;
  }
}

export async function fetchMediaArticle(
  category: MediaArticleCategory,
  slug: string,
  locale: string,
): Promise<Record<string, unknown> | null> {
  const apiCategory = toApiMediaCategory(category);
  const encodedSlug = encodeURIComponent(slug);

  try {
    const response = await fetch(
      `${getApiUrl()}/api/v1/media/${apiCategory}/${encodedSlug}`,
      apiFetchInit(locale),
    );

    if (!response.ok) {
      return null;
    }

    const data = (await response.json()) as { data?: Record<string, unknown> } & Record<string, unknown>;

    return (data.data ?? data) as Record<string, unknown>;
  } catch {
    return null;
  }
}

export async function fetchResources(
  locale: string,
  params: {
    page?: number;
    limit?: number;
    search?: string;
    type?: string;
    year?: number;
    focusArea?: string;
  } = {},
): Promise<{
  items: Record<string, unknown>[];
  meta: PaginatedMeta;
} | null> {
  const query = new URLSearchParams();

  if (params.page) query.set("page", String(params.page));
  if (params.limit) query.set("limit", String(params.limit));
  if (params.search) query.set("search", params.search);
  if (params.type) query.set("type", params.type);
  if (params.year) query.set("year", String(params.year));
  if (params.focusArea) query.set("focusArea", params.focusArea);

  const qs = query.toString();
  const url = `${getApiUrl()}/api/v1/resources${qs ? `?${qs}` : ""}`;

  try {
    const response = await fetch(url, apiFetchInit(locale));

    if (!response.ok) {
      return null;
    }

    const data = (await response.json()) as {
      data: Record<string, unknown>[];
      meta: PaginatedMeta;
    };

    return { items: data.data, meta: data.meta };
  } catch {
    return null;
  }
}

async function fetchAboutEndpoint<T>(path: string, locale: string): Promise<T | null> {
  return apiGet<T>(`/api/v1/about/${path}`, locale);
}

async function fetchStrategyEndpoint<T>(path: string, locale: string): Promise<T | null> {
  return apiGet<T>(`/api/v1/strategy/${path}`, locale);
}

export async function fetchAboutInstitute(locale: string) {
  return fetchAboutEndpoint<{
    heading: string;
    paragraphs: string[];
    headquartersTitle: string;
    statsTitle: string;
    stats: { value: string; label: string; description: string }[];
    tasks: { title: string; items: { description: string }[] };
  }>("institute", locale);
}

export async function fetchAboutVisionMission(locale: string) {
  return fetchAboutEndpoint<{
    vision: { title: string; text: string; readMore: string; image: string };
    mission: { title: string; text: string; readMore: string; image: string };
    goals: { title: string; items: { description: string }[] };
    values: { title: string; items: { title: string; description: string }[] };
  }>("vision-mission", locale);
}

export async function fetchAboutLeadership(type: "president" | "director", locale: string) {
  return fetchAboutEndpoint<{
    honorific?: string;
    name: string;
    position: string;
    quote: string;
    paragraphs: string[];
    image: string;
    imageAlt: string;
  }>(`leadership/${type}`, locale);
}

export async function fetchAboutAdvisoryBoard(locale: string) {
  return fetchAboutEndpoint<{
    readMore: string;
    members: {
      id: string;
      featured?: boolean;
      role: string;
      name: string;
      image: string;
      bio: string;
    }[];
  }>("advisory-board", locale);
}

export async function fetchAboutTeam(locale: string) {
  return fetchAboutEndpoint<{
    readMore: string;
    sections: {
      id: string;
      title: string;
      members: {
        id: string;
        role: string;
        name: string;
        image: string;
        bio: string;
      }[];
    }[];
  }>("team", locale);
}

export async function fetchAboutPartners(locale: string) {
  return fetchAboutEndpoint<{
    heroDescription: string;
    featured: { image: string; name: string }[];
    categories: {
      id: string;
      title: string;
      logos: { image: string; name: string }[];
    }[];
  }>("partners", locale);
}

export async function fetchAboutStructure(locale: string) {
  return fetchAboutEndpoint<{
    imageUrl: string;
    imageAlt: string | null;
  }>("structure", locale);
}

export type ContactInfoPayload = {
  title: string;
  subtitle: string;
  addressLabel: string;
  address: string;
  mapTitle: string;
  mapEmbedUrl: string;
  items: { label: string; value: string; type: string; href?: string }[];
};

export async function fetchContactInfo(locale: string): Promise<ContactInfoPayload | null> {
  return apiGet<ContactInfoPayload>("/api/v1/contact", locale);
}

export async function fetchStrategy2025(locale: string) {
  return fetchStrategyEndpoint<{
    introTitle: string;
    introSubtitle: string;
    booklet: { title: string; pdfUrl: string; href: string };
    pillars: { number: string; text: string }[];
    diagram: { items: { id: string; title: string; content?: string; columns?: string[] }[] };
  }>("strategy-2025", locale);
}

export async function fetchFocusAreas(locale: string) {
  return fetchStrategyEndpoint<{
    pages: { title: string; back: string; viewMore: string; previous?: string; next?: string };
    items: {
      slug: string;
      number: string;
      title: string;
      highlight: string;
      tags: string[];
      description: string;
      listImage: string;
      detailImage: string;
    }[];
  }>("focus-areas", locale);
}

export async function fetchFocusArea(slug: string, locale: string) {
  return fetchStrategyEndpoint<{
    area: {
      slug: string;
      number: string;
      title: string;
      highlight: string;
      tags: string[];
      description: string;
      listImage: string;
      detailImage: string;
    };
    navigation: {
      previous: { slug: string; title: string } | null;
      next: { slug: string; title: string } | null;
    };
  }>(`focus-areas/${encodeURIComponent(slug)}`, locale);
}

export type ProgramPayload = {
  slug: string;
  title: string;
  heroIntro: string;
  back: string;
  sectionsLabel: string;
  tabs: { id: string; label: string }[];
  sections: Record<string, Record<string, unknown>>;
};

export async function fetchProgram(
  slug: string,
  locale: string,
): Promise<ProgramPayload | null> {
  return apiGet<ProgramPayload>(`/api/v1/programs/${slug}`, locale);
}

export type PortalContributionType = "publications" | "cities" | "organizations";

export type DirectoryTab = "cities" | "projects" | "organizations" | "publications";

export async function fetchDirectory(
  tab: DirectoryTab,
  locale: string,
  params: { page?: number; limit?: number; search?: string } = {},
) {
  const query = new URLSearchParams({ tab });
  if (params.page) query.set("page", String(params.page));
  if (params.limit) query.set("limit", String(params.limit));
  if (params.search) query.set("search", params.search);

  try {
    const response = await fetch(
      `${getApiUrl()}/api/v1/programs/urban-policies/directory?${query.toString()}`,
      apiFetchInit(locale),
    );

    if (!response.ok) {
      return null;
    }

    return (await response.json()) as {
      tab: string;
      meta: PaginatedMeta;
      ui: Record<string, unknown>;
      data: Record<string, unknown>[];
    };
  } catch {
    return null;
  }
}

export async function fetchAllDirectoryRows(locale: string) {
  const tabs: DirectoryTab[] = ["cities", "projects", "organizations", "publications"];

  const results = await Promise.all(
    tabs.map((tab) => fetchDirectory(tab, locale, { limit: 100 })),
  );

  return Object.fromEntries(
    tabs.map((tab, index) => [tab, results[index]?.data ?? []]),
  ) as Record<DirectoryTab, Record<string, unknown>[]>;
}

export type DirectoryItemDetail = {
  tab: DirectoryTab;
  number: string;
  item: Record<string, unknown>;
  discussions: { id: number; author: string; body: string; createdAt?: string }[];
  ui: {
    discussionTitle?: string;
    addCommentLabel?: string;
    authorNameLabel?: string;
    commentBodyLabel?: string;
    submitCommentLabel?: string;
    backToListLabel?: string;
    shareLabel?: string;
    downloadLabel?: string;
    addressLabel?: string;
    sourceLabel?: string;
    relatedProjectsTitle?: string;
  };
};

export async function fetchDirectoryItem(
  tab: DirectoryTab,
  number: string,
  locale: string,
): Promise<DirectoryItemDetail | null> {
  return apiGet<DirectoryItemDetail>(
    `/api/v1/programs/urban-policies/directory/${tab}/${encodeURIComponent(number)}`,
    locale,
  );
}

export async function submitDirectoryDiscussion(
  tab: DirectoryTab,
  number: string,
  payload: { authorName: string; body: string },
  locale: string,
): Promise<{ message: string; data: { id: number; author: string; body: string } }> {
  return apiPost(
    `/api/v1/programs/urban-policies/directory/${tab}/${encodeURIComponent(number)}/discussions`,
    locale,
    payload,
  );
}

export async function submitPortalContribution(
  payload: {
    type: PortalContributionType;
    email: string;
    payload: Record<string, unknown>;
  },
  locale: string,
): Promise<{ message: string; id: number }> {
  return apiPost("/api/v1/programs/urban-policies/contribute", locale, payload);
}

export async function submitMembershipApplication(
  payload: {
    organizationName: string;
    contactName: string;
    email: string;
    phone: string;
    countryCode?: string;
    city?: string;
    message?: string;
  },
  locale: string,
): Promise<{ message: string; id: number }> {
  return apiPost("/api/v1/membership", locale, payload);
}

export async function submitContactForm(
  payload: {
    name: string;
    phone: string;
    email: string;
    message: string;
  },
  locale: string,
): Promise<{ message: string; id: number }> {
  return apiPost("/api/v1/contact", locale, payload);
}

// --- Footer features: FAQ / Careers / Legal ---

export type FaqItem = {
  id: number;
  category: string | null;
  question: string;
  answer: string;
};

export async function fetchFaqs(
  locale: string,
  category?: string,
): Promise<FaqItem[]> {
  const query = category ? `?category=${encodeURIComponent(category)}` : "";
  const result = await apiGet<{ data: FaqItem[] }>(`/api/v1/faqs${query}`, locale);
  return result?.data ?? [];
}

export type JobOpening = {
  id: number;
  title: string;
  location: string | null;
  employmentType: string;
  summary: string | null;
  description: string[];
  publishedDate: string | null;
};

export async function fetchJobOpenings(locale: string): Promise<JobOpening[]> {
  const result = await apiGet<{ data: JobOpening[] }>("/api/v1/careers", locale);
  return result?.data ?? [];
}

export async function fetchJobOpening(
  id: number | string,
  locale: string,
): Promise<JobOpening | null> {
  const result = await apiGet<{ data: JobOpening }>(`/api/v1/careers/${id}`, locale);
  return result?.data ?? null;
}

export async function submitJobApplication(
  payload: {
    jobOpeningId?: number | null;
    fullName: string;
    email: string;
    phone?: string;
    coverLetter?: string;
    cvUrl?: string;
  },
  locale: string,
): Promise<{ message: string; id: number }> {
  return apiPost("/api/v1/careers/apply", locale, payload);
}

export type LegalPage = {
  slug: string;
  title: string;
  content: string;
  effectiveDate: string | null;
  updatedAt: string | null;
};

export async function fetchLegalPage(
  slug: "terms" | "privacy" | string,
  locale: string,
): Promise<LegalPage | null> {
  const result = await apiGet<{ data: LegalPage }>(
    `/api/v1/legal/${encodeURIComponent(slug)}`,
    locale,
  );
  return result?.data ?? null;
}

// --- Settings + newsletter ---

export type SocialLinkItem = {
  platform: string;
  url: string;
  icon: string | null;
};

export type SiteSettingsPayload = {
  siteName: string;
  copyright: string | null;
  socialLinks: SocialLinkItem[];
  contact: ContactInfoPayload;
};

export async function fetchSettings(
  locale: string,
): Promise<SiteSettingsPayload | null> {
  const result = await apiGet<{ data: SiteSettingsPayload }>(
    "/api/v1/settings",
    locale,
  );
  return result?.data ?? null;
}

export async function subscribeNewsletter(
  payload: { email: string; locale?: string },
  locale: string,
): Promise<{ message: string; id: number; isNew: boolean }> {
  return apiPost("/api/v1/newsletter/subscribe", locale, payload);
}
