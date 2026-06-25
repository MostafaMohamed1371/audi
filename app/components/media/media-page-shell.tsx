import { MediaHeroVideo } from "@/app/components/media/media-hero-video";
import { MediaTabs } from "@/app/components/media/media-tabs";
import type { MediaTab } from "@/lib/media";
import { getLocale, getTranslations } from "next-intl/server";

const MEDIA_HERO_OVERLAY =
  "linear-gradient(180deg, rgba(17, 31, 66, 0.2376) 0%, rgba(0, 0, 0, 0.54) 100%)";

type Props = {
  activeTab: MediaTab;
  children: React.ReactNode;
};

export async function MediaPageShell({ activeTab, children }: Props) {
  const t = await getTranslations("media");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const tabs = [
    { id: "news" as const, label: t("tabs.news"), href: "/media/news" as const },
    {
      id: "newsletter" as const,
      label: t("tabs.newsletter"),
      href: "/media/newsletter" as const,
    },
    {
      id: "cityMeetings" as const,
      label: t("tabs.cityMeetings"),
      href: "/media/city-meetings" as const,
    },
    {
      id: "secretarySpeaks" as const,
      label: t("tabs.secretarySpeaks"),
      href: "/media/secretary-speaks" as const,
    },
  ];

  return (
    <div className="bg-background">
      <section className="relative overflow-hidden">
        <div className="relative min-h-[320px] sm:min-h-[380px] lg:min-h-[450px]">
          <MediaHeroVideo src="/header/media-center.mp4" />

          <div
            className="absolute inset-0"
            style={{ background: MEDIA_HERO_OVERLAY }}
            aria-hidden
          />

          <div className="relative z-10 mx-auto flex min-h-[320px] max-w-7xl items-end px-4 pb-20 pt-32 sm:min-h-[380px] sm:px-6 sm:pb-24 sm:pt-36 lg:min-h-[450px] lg:pb-28 lg:pt-40">
            <div
              dir={isRtl ? "rtl" : "ltr"}
              className="max-w-3xl space-y-4 text-start"
            >
              <h1 className="text-3xl font-bold text-white sm:text-4xl lg:text-5xl">
                {t("pages.title")}
              </h1>
              <p className="text-sm leading-8 text-white/90 sm:text-base sm:leading-9">
                {t("pages.subtitle")}
              </p>
            </div>
          </div>
        </div>
      </section>

      <MediaTabs tabs={tabs} activeTab={activeTab} />

      <div className="px-4 pb-16 pt-6 sm:px-6 sm:pb-20 sm:pt-8">
        <div className="mx-auto max-w-7xl">{children}</div>
      </div>
    </div>
  );
}
