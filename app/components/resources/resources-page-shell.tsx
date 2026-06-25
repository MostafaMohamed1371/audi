import Image from "next/image";
import { ResourcesHeroFilters } from "@/app/components/resources/resources-hero-filters";
import { getLocale, getTranslations } from "next-intl/server";

const RESOURCES_HERO_OVERLAY =
  "linear-gradient(180deg, rgba(17, 31, 66, 0.44) 0%, #000000 100%)";

type Props = {
  children: React.ReactNode;
};

export async function ResourcesPageShell({ children }: Props) {
  const t = await getTranslations("resources");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  return (
    <div className="bg-background">
      <section className="relative overflow-hidden">
        <div className="relative min-h-[360px] sm:min-h-[420px] lg:min-h-[480px]">
          <Image
            src="/header/our-sources.png"
            alt=""
            fill
            priority
            className="object-cover"
            sizes="100vw"
          />

          <div
            className="absolute inset-0"
            style={{ background: RESOURCES_HERO_OVERLAY }}
            aria-hidden
          />

          <div className="relative z-10 mx-auto flex min-h-[360px] max-w-7xl flex-col justify-between px-4 pb-8 pt-32 sm:min-h-[420px] sm:px-6 sm:pb-10 sm:pt-36 lg:min-h-[480px] lg:pb-12 lg:pt-40">
            <h1
              dir={isRtl ? "rtl" : "ltr"}
              className="text-start text-3xl font-bold text-white sm:text-4xl lg:text-5xl"
            >
              {t("pages.title")}
            </h1>

            <ResourcesHeroFilters />
          </div>
        </div>
      </section>

      <div className="px-4 py-12 sm:px-6 sm:py-16 lg:py-20">
        <div className="mx-auto max-w-7xl">{children}</div>
      </div>
    </div>
  );
}
