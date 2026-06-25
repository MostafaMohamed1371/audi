import Image from "next/image";
import { getLocale, getTranslations } from "next-intl/server";
import { Link, type AppHref } from "@/i18n/routing";
import { FooterBackToTop } from "@/app/components/layout/footer-back-to-top";
import { FooterNewsletterForm } from "@/app/components/layout/footer-newsletter-form";
import { SocialIcon } from "@/app/components/layout/social-icons";
import { fetchSettings } from "@/lib/api";
import { cn } from "@/lib/utils";

type FooterLink = {
  href: AppHref;
  labelKey: string;
};

type FooterColumn = {
  titleKey: string;
  links: FooterLink[];
};

const SOCIAL_ICON_NAMES = [
  "facebook",
  "linkedin",
  "youtube",
  "instagram",
  "x",
] as const;

type SocialIconName = (typeof SOCIAL_ICON_NAMES)[number];

function resolveSocialIcon(icon: string | null, platform: string): SocialIconName {
  const candidate = (icon ?? platform).toLowerCase();
  return SOCIAL_ICON_NAMES.includes(candidate as SocialIconName)
    ? (candidate as SocialIconName)
    : "facebook";
}

export async function Footer() {
  const t = await getTranslations("common");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const year = new Date().getFullYear();
  const settings = await fetchSettings(locale);

  const socialLinks =
    settings?.socialLinks?.filter((link) => link.url) ??
    SOCIAL_ICON_NAMES.map((icon) => ({
      platform: icon,
      url: "#",
      icon,
    }));

  const copyright =
    settings?.copyright != null
      ? `${settings.copyright} ${year}`
      : t("footer.copyright", { year });

  const columns: FooterColumn[] = [
    {
      titleKey: "footer.about.title",
      links: [
        { href: "/about", labelKey: "footer.about.aboutInstitute" },
        {
          href: "/about/vision-mission",
          labelKey: "footer.about.visionMission",
        },
        {
          href: "/about/president-speech",
          labelKey: "nav.aboutPresidentSpeech",
        },
        {
          href: "/about/director-message",
          labelKey: "nav.aboutDirectorMessage",
        },
        {
          href: "/about/advisory-board",
          labelKey: "nav.aboutAdvisoryBoard",
        },
        { href: "/about/team", labelKey: "nav.aboutTeam" },
        { href: "/about/structure", labelKey: "footer.about.structure" },
        { href: "/about/partners", labelKey: "nav.aboutPartners" },
      ],
    },
    {
      titleKey: "footer.strategy.title",
      links: [
        {
          href: "/strategy/strategy-2025",
          labelKey: "footer.strategy.strategy2025",
        },
        {
          href: "/strategy/focus-areas",
          labelKey: "footer.strategy.focusAreas",
        },
      ],
    },
    {
      titleKey: "footer.programs.title",
      links: [
        {
          href: "/programs/urban-policies",
          labelKey: "footer.programs.urbanPolicies",
        },
        { href: "/programs/training", labelKey: "footer.programs.training" },
        {
          href: "/programs/partnerships",
          labelKey: "footer.programs.partnerships",
        },
      ],
    },
    {
      titleKey: "footer.media.title",
      links: [
        { href: "/media/news", labelKey: "footer.media.news" },
        { href: "/media/newsletter", labelKey: "footer.media.newsletter" },
        { href: "/media/city-meetings", labelKey: "footer.media.cityMeetings" },
        {
          href: "/media/secretary-speaks",
          labelKey: "footer.media.secretarySpeaks",
        },
      ],
    },
    {
      titleKey: "footer.community.title",
      links: [
        { href: { pathname: "/", hash: "membership" }, labelKey: "footer.community.joinUs" },
        { href: "/careers", labelKey: "footer.community.workWithUs" },
      ],
    },
    {
      titleKey: "footer.quickLinks.title",
      links: [
        { href: "/resources", labelKey: "nav.resources" },
        { href: "/contact", labelKey: "nav.contact" },
        { href: "/legal/terms", labelKey: "footer.quickLinks.terms" },
        { href: "/legal/privacy", labelKey: "footer.quickLinks.privacy" },
        { href: "/faq", labelKey: "footer.quickLinks.faq" },
      ],
    },
  ];

  return (
    <footer id="contact" className="bg-primary text-white">
      <div className="mx-auto w-full max-w-[1600px] px-[80px] py-14 sm:py-16 pb-4!">
        <div className="flex flex-col gap-12 lg:flex-row lg:items-start lg:justify-between lg:gap-10 xl:gap-12">
          <div
            className={cn(
              "shrink-0 space-y-6",
              isRtl ? "text-right" : "text-left",
            )}
          >
            <Link href="/">
              <Image
                src="/logo.png"
                alt={settings?.siteName ?? t("siteName")}
                width={180}
                height={90}
                className="h-[72px] w-auto object-contain brightness-0 invert"
              />
            </Link>
            <div className="space-y-3">
              <p className="text-sm font-medium text-white">
                {t("footer.followUs")}
              </p>
              <div className="flex items-center gap-3">
                {socialLinks.map((link) => {
                  const icon = resolveSocialIcon(link.icon, link.platform);
                  const label =
                    link.platform.charAt(0).toUpperCase() + link.platform.slice(1);

                  return (
                    <a
                      key={`${link.platform}-${link.url}`}
                      href={link.url}
                      aria-label={label}
                      target={link.url.startsWith("http") ? "_blank" : undefined}
                      rel={
                        link.url.startsWith("http")
                          ? "noopener noreferrer"
                          : undefined
                      }
                      className="text-white/85 transition-colors hover:text-white"
                    >
                      <SocialIcon name={icon} />
                    </a>
                  );
                })}
              </div>
            </div>
            <FooterNewsletterForm isRtl={isRtl} />
          </div>

          <div className="grid min-w-0 flex-1 grid-cols-2 gap-x-8 gap-y-10 sm:grid-cols-3 lg:grid-cols-6 lg:gap-x-6 xl:gap-x-10">
            {columns.map((column) => (
              <div
                key={column.titleKey}
                className={isRtl ? "text-right" : "text-left"}
              >
                <h3 className="mb-4 text-sm font-bold text-white">
                  {t(column.titleKey)}
                </h3>
                <ul className="space-y-2.5">
                  {column.links.map((link) => (
                    <li key={link.labelKey}>
                      <Link
                        href={link.href}
                        className="text-sm text-white/75 transition-colors hover:text-white"
                      >
                        {t(link.labelKey)}
                      </Link>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>

          <div className="flex shrink-0 lg:self-center">
            <FooterBackToTop label={t("footer.backToTop")} />
          </div>
        </div>

        <div className="mt-12 border-t border-white/15 pt-3 text-center">
          <p className="text-sm text-white/70">{copyright}</p>
        </div>
      </div>
    </footer>
  );
}
