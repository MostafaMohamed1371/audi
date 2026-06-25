import type { PartnershipSectionContent } from "@/app/components/programs/partnerships/shared/types";
import type { PortalDirectoryTab } from "@/lib/programs-urban-policies-directory";

export type UrbanPoliciesSectionContent = PartnershipSectionContent;

export type PortalDirectoryCityRow = {
  number: string;
  name: string;
  description: string;
};

export type PortalDirectoryProjectRow = {
  number: string;
  city: string;
  country: string;
  startDate: string;
  endDate: string;
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
  };
  rows: Record<
    PortalDirectoryTab,
    PortalDirectoryCityRow[] | PortalDirectoryProjectRow[]
  >;
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
