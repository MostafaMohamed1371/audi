import {
  ResourcesCardsGrid,
  type ResourceItem,
} from "@/app/components/resources/resources-cards-grid";
import { fetchResources } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

export async function ResourcesContent() {
  const t = await getTranslations("resources");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const apiResult = await fetchResources(locale, { limit: 50 });
  const fallbackItems = t.raw("items") as ResourceItem[];
  const items: ResourceItem[] = apiResult
    ? (apiResult.items as ResourceItem[])
    : fallbackItems;

  return (
    <div className="space-y-10 sm:space-y-12">
      <h2 className="text-center text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
        {t("pages.sectionTitle")}
      </h2>

      <ResourcesCardsGrid
        items={items}
        downloadLabel={t("download")}
        isRtl={isRtl}
      />
    </div>
  );
}
