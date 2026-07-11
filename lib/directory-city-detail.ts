import type { DirectoryItemDetail } from "@/lib/api";
import {
  directoryCityNumberFromSlug,
  type DirectoryCitySlug,
} from "@/lib/directory-cities";

type CityRow = {
  number: string;
  slug?: string;
  name: string;
  description?: string;
  detail?: Record<string, unknown>;
  discussions?: { author: string; body: string }[];
};

type ProgramsMessages = {
  urbanPolicies: {
    developmentPortal: {
      directory: {
        rows: {
          cities: CityRow[];
        };
      };
    };
  };
};

export async function getFallbackDirectoryCityDetail(
  locale: string,
  slug: DirectoryCitySlug,
): Promise<DirectoryItemDetail | null> {
  const number = directoryCityNumberFromSlug(slug);
  if (!number) {
    return null;
  }

  const programs = (await import(`../messages/${locale}/programs.json`))
    .default as ProgramsMessages;
  const row = programs.urbanPolicies.developmentPortal.directory.rows.cities.find(
    (city) => city.slug === slug,
  );

  if (!row) {
    return null;
  }

  let detail = row.detail;
  if (!detail) {
    try {
      detail = (
        await import(`../messages/data/${slug}-detail.${locale}.json`)
      ).default as Record<string, unknown>;
    } catch {
      return null;
    }
  }

  const layout = detail.layout === "rich" ? "rich" : "simple";
  const discussions =
    layout === "rich"
      ? (row.discussions ?? []).map((discussion, index) => ({
          id: index + 1,
          author: discussion.author,
          body: discussion.body,
        }))
      : [];

  return {
    tab: "cities",
    number,
    item: {
      number: row.number,
      name: row.name,
      description: row.description,
      slug,
      detail,
    },
    discussions,
    ui: {},
  };
}
