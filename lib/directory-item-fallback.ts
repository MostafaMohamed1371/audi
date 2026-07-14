import type { DirectoryItemDetail, DirectoryTab } from "@/lib/api";

type DirectoryRow = Record<string, unknown> & {
  number: string;
  discussions?: { author: string; body: string }[];
  detail?: Record<string, unknown>;
};

type ProgramsMessages = {
  urbanPolicies: {
    developmentPortal: {
      directory: {
        rows: Record<string, DirectoryRow[]>;
      };
    };
  };
};

const ORGANIZATION_PROFILE_KEYS = [
  "type",
  "country",
  "countryCode",
  "address",
  "phone",
  "email",
  "website",
  "founded",
  "employees",
  "budget",
  "interventionAreas",
  "interventionFields",
  "interventionTypes",
  "socialLinks",
] as const;

const PROJECT_PROFILE_KEYS = [
  "slug",
  "layout",
  "heroImage",
  "mapImage",
  "valuesContent",
  "policyToolsContent",
  "sources",
  "founders",
  "references",
  "relatedProjects",
] as const;

const PUBLICATION_PROFILE_KEYS = [
  "organizationName",
  "organizationType",
  "publicationCountry",
  "languages",
  "publicationDate",
  "publicationType",
  "topics",
  "publicationLink",
  "coverImage",
  "languageVersions",
] as const;

function pickProfile(row: DirectoryRow, keys: readonly string[]) {
  return Object.fromEntries(
    keys.filter((key) => row[key] !== undefined).map((key) => [key, row[key]]),
  );
}

export async function getFallbackDirectoryItem(
  locale: string,
  tab: DirectoryTab,
  number: string,
): Promise<DirectoryItemDetail | null> {
  const programs = (await import(`../messages/${locale}/programs.json`))
    .default as ProgramsMessages;
  const rows = programs.urbanPolicies.developmentPortal.directory.rows[tab] ?? [];
  const row = rows.find((entry) => entry.number === number);

  if (!row) {
    return null;
  }

  if (tab === "projects") {
    const profile = pickProfile(row, PROJECT_PROFILE_KEYS);
    const city = String(row.city ?? "");
    const country = String(row.country ?? "");

    return {
      tab,
      number,
      item: {
        number: row.number,
        city,
        country,
        startDate: row.startDate,
        endDate: row.endDate,
        title: `${city}, ${country}`,
        detail: row.detail ?? profile,
        ...profile,
      },
      discussions: [],
      ui: {},
    };
  }

  if (tab === "publications") {
    const profile = pickProfile(row, PUBLICATION_PROFILE_KEYS);
    const name = String(row.name ?? "");

    return {
      tab,
      number,
      item: {
        number: row.number,
        name,
        description: row.description,
        detail: row.detail ?? profile,
        ...profile,
      },
      discussions: [],
      ui: {},
    };
  }

  if (tab === "organizations") {
    const profile = pickProfile(row, ORGANIZATION_PROFILE_KEYS);
    const name = String(row.name ?? "");

    return {
      tab,
      number,
      item: {
        number: row.number,
        name,
        description: row.type ?? row.description,
        detail: row.detail ?? profile,
        ...profile,
      },
      discussions: [],
      ui: {},
    };
  }

  const discussions = (row.discussions ?? []).map((discussion, index) => ({
    id: index + 1,
    author: discussion.author,
    body: discussion.body,
  }));

  return {
    tab,
    number,
    item: row,
    discussions,
    ui: {},
  };
}
