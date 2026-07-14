import type { PartnershipSectionContent } from "@/app/components/programs/partnerships/shared/types";
import type { PortalDirectoryTab } from "@/lib/programs-urban-policies-directory";

export type UrbanPoliciesSectionContent = PartnershipSectionContent;

export type UrbanPoliciesProjectItem = {
  title: string;
  date: string;
  image: string;
  href: string;
};

export type UrbanPoliciesReportsContent = PartnershipSectionContent & {
  video?: string;
  videoPoster?: string;
  projectsTitle?: string;
  viewIssue?: string;
  projects?: UrbanPoliciesProjectItem[];
};

export type PortalDirectoryCityRow = {
  number: string;
  slug?: string;
  name: string;
  description: string;
};

export type PortalDirectoryProjectRow = {
  number: string;
  slug?: string;
  city: string;
  country: string;
  startDate: string;
  endDate: string;
  layout?: "simple" | "rich";
  heroImage?: string;
  mapImage?: string;
  valuesContent?: string;
  policyToolsContent?: string;
  sources?: { name: string; image: string }[];
  founders?: { role: string; name: string }[];
  references?: {
    projectLink?: string;
    notes?: string;
    references?: string[];
  };
  relatedProjects?: {
    city: string;
    country: string;
    dateRange: string;
    image?: string;
  }[];
};

export type PortalDirectoryOrganizationRow = {
  number: string;
  name: string;
  type: string;
  country: string;
  countryCode?: string;
  address?: string;
  phone?: string;
  email?: string;
  website?: string;
  founded?: string;
  employees?: string;
  budget?: string;
  interventionAreas?: string;
  interventionFields?: string[];
  interventionTypes?: string[];
  socialLinks?: { platform: string; href: string }[];
};

export type PortalDirectoryPublicationRow = {
  number: string;
  name: string;
  description: string;
  organizationName?: string;
  organizationType?: string;
  publicationCountry?: string;
  languages?: { code: string; label: string }[];
  publicationDate?: string;
  publicationType?: string;
  topics?: string[];
  publicationLink?: string;
  coverImage?: string;
  languageVersions?: {
    ar?: { label: string; href?: string };
    en?: { label: string; href?: string };
  };
};

export type PortalDirectoryPublicationFields = {
  detailsTitle: string;
  organizationName: string;
  organizationType: string;
  publicationCountry: string;
  languages: string;
  publicationDate: string;
  publicationType: string;
  topics: string;
  publicationLink: string;
  inArabicLabel: string;
  inEnglishLabel: string;
};

export type PortalDirectoryOrganizationFields = {
  address: string;
  phone: string;
  email: string;
  website: string;
  type: string;
  founded: string;
  employees: string;
  budget: string;
  interventionAreas: string;
  interventionFields: string;
  interventionTypes: string;
};

export type PortalDirectoryContent = {
  video: string;
  videoPoster?: string;
  title: string;
  subtitle: string;
  filtersTitle: string;
  countryLabel: string;
  cityLabel: string;
  citySizeLabel: string;
  resetLabel: string;
  searchLabel: string;
  viewListLabel: string;
  viewMapLabel: string;
  mapPlaceholder: string;
  seeMoreLabel: string;
  shareLabel?: string;
  downloadLabel?: string;
  addressLabel?: string;
  sourceLabel?: string;
  relatedProjectsTitle?: string;
  discussionTitle?: string;
  addCommentLabel?: string;
  authorNameLabel?: string;
  commentBodyLabel?: string;
  submitCommentLabel?: string;
  backToListLabel?: string;
  commentSuccess?: string;
  commentError?: string;
  organizationFields?: PortalDirectoryOrganizationFields;
  publicationFields?: PortalDirectoryPublicationFields;
  projectDescriptionTitle?: string;
  projectValuesTitle?: string;
  projectPolicyToolsTitle?: string;
  viewSourceLabel?: string;
  foundersTitle?: string;
  referencesAccordionTitle?: string;
  projectLinkLabel?: string;
  notesLabel?: string;
  referencesLabel?: string;
  cta?: {
    title: string;
    description: string;
    button: string;
    href?: string;
  };
  tabs: { id: PortalDirectoryTab; label: string }[];
  columns: {
    cities: { number: string; name: string; details: string };
    projects: {
      number: string;
      city: string;
      country: string;
      startDate: string;
      endDate: string;
    };
    organizations: { number: string; organization: string; details: string };
    publications: { number: string; publication: string; details: string };
  };
  rows: {
    cities: PortalDirectoryCityRow[];
    projects: PortalDirectoryProjectRow[];
    organizations: PortalDirectoryOrganizationRow[];
    publications: PortalDirectoryPublicationRow[];
  };
};

export type DevelopmentPortalContent = {
  title: string;
  paragraphs: string[];
  contributeTitle: string;
  contributeDescription: string;
  email: string;
  image: string;
  illustration?: string;
  ctaTitle: string;
  ctaDisclaimer: string;
  contributionTypes: {
    id: string;
    label: string;
  }[];
  directory: PortalDirectoryContent;
};
