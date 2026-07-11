import type { DirectoryItemDetail, DirectoryTab } from "@/lib/api";

type OrganizationRow = {
  number: string;
  name: string;
  type?: string;
  country?: string;
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
  detail?: Record<string, unknown>;
  discussions?: { author: string; body: string }[];
};

type ProgramsMessages = {
  urbanPolicies: {
    developmentPortal: {
      directory: {
        rows: Record<string, OrganizationRow[]>;
      };
    };
  };
};

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

  const profileKeys = [
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

  const profile = Object.fromEntries(
    profileKeys
      .filter((key) => row[key] !== undefined)
      .map((key) => [key, row[key]]),
  );

  const detail = row.detail ?? (tab === "organizations" ? profile : undefined);
  const discussions = (row.discussions ?? []).map((discussion, index) => ({
    id: index + 1,
    author: discussion.author,
    body: discussion.body,
  }));

  return {
    tab,
    number,
    item: {
      number: row.number,
      name: row.name,
      description: row.type ?? (row as { description?: string }).description,
      detail,
      ...profile,
    },
    discussions,
    ui: {},
  };
}
