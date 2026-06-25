import { AboutIntroSection } from "@/app/components/home/about-intro/about-intro-section";
import { HeroSlider } from "@/app/components/home/hero/hero-slider";
import { KnowledgeCenterSection } from "@/app/components/home/knowledge-center/knowledge-center-section";
import { MediaCenterSection } from "@/app/components/home/media-center/media-center-section";
import { MemberCitiesSection } from "@/app/components/home/member-cities/member-cities-section";
import { MembershipContactSection } from "@/app/components/home/membership-contact/membership-contact-section";
import { ProgramsSection } from "@/app/components/home/programs/programs-section";
import { StatsSection } from "@/app/components/home/stats/stats-section";
import { fetchHome } from "@/lib/api";
import { getSliderImages } from "@/lib/slider-images";
import { getTranslations, setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

type SlideTranslation = {
  title: string;
};

export default async function HomePage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const t = await getTranslations("home");
  const home = await fetchHome(locale);
  const slideTranslations = t.raw("slider.slides") as SlideTranslation[];
  const fallbackImages = getSliderImages();
  const apiSlides = home?.slider ?? [];

  const slideCount = Math.max(
    apiSlides.length,
    fallbackImages.length,
    slideTranslations.length,
  );

  const slides = Array.from({ length: slideCount }, (_, index) => ({
    image:
      apiSlides[index]?.imageUrl ??
      fallbackImages[index % Math.max(fallbackImages.length, 1)] ??
      `/slider/${(index % 4) + 1}.png`,
    title:
      apiSlides[index]?.title ??
      slideTranslations[index]?.title ??
      `Slide ${index + 1}`,
  }));

  return (
    <div className="overflow-x-hidden">
      <HeroSlider slides={slides} />
      <AboutIntroSection aboutIntro={home?.aboutIntro} />
      <StatsSection
        title={home?.stats.title}
        subtitle={home?.stats.subtitle}
        items={home?.stats.items}
      />
      <MemberCitiesSection
        title={home?.memberCities.title}
        stats={home?.memberCities.stats}
      />
      <ProgramsSection programs={home?.programs} />
      <MediaCenterSection mediaCenter={home?.mediaCenter} />
      <KnowledgeCenterSection knowledgeCenter={home?.knowledgeCenter} />
      <MembershipContactSection membershipContact={home?.membershipContact} />
    </div>
  );
}
