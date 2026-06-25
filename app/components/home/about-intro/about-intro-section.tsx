import Image from "next/image";
import { getTranslations } from "next-intl/server";
import { Link } from "@/i18n/routing";
import { ButtonLink } from "@/app/components/ui/button";
import { ArrowLeft } from "lucide-react";
import { VisionMissionCards } from "@/app/components/home/about-intro/vision-mission-cards";
import type { HomePayload } from "@/lib/api";

type Props = {
  aboutIntro?: HomePayload["aboutIntro"];
};

export async function AboutIntroSection({ aboutIntro }: Props = {}) {
  const t = await getTranslations("home.aboutIntro");

  return (
    <section className="bg-background">
      <div className="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:gap-10 sm:px-6 sm:py-16 lg:grid-cols-2 lg:items-center lg:gap-16 lg:py-20">
        <h2 className="text-2xl font-bold leading-tight text-secondary sm:text-3xl lg:text-[2.5rem]">
          {aboutIntro?.title ?? t("title")}
        </h2>

        <div className="space-y-6 sm:space-y-8">
          <p className="text-sm leading-7 text-muted-foreground sm:text-base sm:leading-8 lg:text-lg">
            {aboutIntro?.description ?? t("description")}
          </p>
          <ButtonLink
            size="lg"
            className="rounded-full px-8"
            render={<Link href="/about" />}
          >
            {aboutIntro?.cta ?? t("cta")}
            <ArrowLeft className="size-4" />
          </ButtonLink>
        </div>
      </div>

      <div className="bg-linear-to-b from-background via-background to-[#eef6f9] pb-12 pt-2 sm:pb-24 lg:pb-28">
        <div className="relative mx-auto max-w-7xl px-4 sm:px-6">
          <div className="relative h-[220px] overflow-hidden sm:h-[380px] lg:h-[440px]">
            <Image
              src="/home/h-about-hero.png"
              alt=""
              fill
              priority
              className="object-cover object-center"
              sizes="(max-width: 1280px) 100vw, 1280px"
            />
          </div>

          <div className="relative z-10 -mt-12 sm:-mt-[8.5rem] lg:-mt-[9.5rem]">
            <VisionMissionCards
              mission={{
                title: aboutIntro?.mission.title ?? t("mission.title"),
                description: aboutIntro?.mission.description ?? t("mission.description"),
                readMore: aboutIntro?.mission.readMore ?? t("mission.readMore"),
              }}
              vision={{
                title: aboutIntro?.vision.title ?? t("vision.title"),
                description: aboutIntro?.vision.description ?? t("vision.description"),
                readMore: aboutIntro?.vision.readMore ?? t("vision.readMore"),
              }}
            />
          </div>
        </div>
      </div>
    </section>
  );
}
