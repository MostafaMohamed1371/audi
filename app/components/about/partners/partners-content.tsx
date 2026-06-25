import { PartnersCategories } from "@/app/components/about/partners/partners-categories";
import { PartnersHero } from "@/app/components/about/partners/partners-hero";
import { PartnersLogoSlider } from "@/app/components/about/partners/partners-logo-slider";
import { fetchAboutPartners } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

export async function PartnersContent() {
  const pagesT = await getTranslations("about.pages");
  const t = await getTranslations("about.partners");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const apiData = await fetchAboutPartners(locale);

  const featured = apiData?.featured ?? (t.raw("featured") as { image: string; name: string }[]);
  const categories = apiData?.categories ?? (t.raw("categories") as {
    id: string;
    title: string;
    logos: { image: string; name: string }[];
  }[]);

  return (
    <div className="bg-white">
      <PartnersHero
        title={pagesT("partners")}
        description={apiData?.heroDescription ?? t("heroDescription")}
        isRtl={isRtl}
        image="/header/2.png"
        backgroundColor="#000000B8"
        minHeightClassName="min-h-[320px] sm:min-h-[400px] lg:min-h-[500px]"
      />
      <PartnersLogoSlider logos={featured} isRtl={isRtl} />
      <PartnersCategories categories={categories} isRtl={isRtl} />
    </div>
  );
}
