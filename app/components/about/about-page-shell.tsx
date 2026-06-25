import { PageHeroHeader } from "@/app/components/layout/page-hero-header";
import { getTranslations } from "next-intl/server";

type AboutPageShellProps = {
  titleKey: string;
  image?: string;
  children?: React.ReactNode;
  backgroundColor?: string;
  heroClassName?: string;
  heroContentClassName?: string;
  heroTitleClassName?: string;
};

export async function AboutPageShell({
  titleKey,
  image,
  children,
  backgroundColor,
  heroClassName,
  heroContentClassName,
  heroTitleClassName,
}: AboutPageShellProps) {
  const t = await getTranslations("about.pages");

  return (
    <div className="bg-background">
      <PageHeroHeader
        title={t(titleKey)}
        image={image}
        backgroundColor={backgroundColor}
        className={heroClassName}
        contentClassName={heroContentClassName}
        titleClassName={heroTitleClassName}
      />

      {children ?? (
        <section className="px-4 py-16 sm:px-6 sm:py-20">
          <div className="mx-auto max-w-4xl">
            <p className="text-muted-foreground">{t("placeholder")}</p>
          </div>
        </section>
      )}
    </div>
  );
}
