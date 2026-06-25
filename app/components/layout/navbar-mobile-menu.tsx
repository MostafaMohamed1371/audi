"use client";

import { ButtonLink } from "@/app/components/ui/button";
import { Link, usePathname, type AppHref } from "@/i18n/routing";
import { cn } from "@/lib/utils";
import { ChevronDown, X } from "lucide-react";
import { useTranslations } from "next-intl";
import { useEffect, useState } from "react";

function hrefPath(href: AppHref) {
  return typeof href === "string" ? href.split("#")[0] : href.pathname;
}

function isNavActive(pathname: string, href: AppHref) {
  const path = hrefPath(href);
  if (path === "/") return pathname === "/";
  return pathname === path || pathname.startsWith(`${path}/`);
}

type NavGroup = {
  labelKey: string;
  groupPrefix?: string;
  items: { href: AppHref; labelKey: string }[];
};

const NAV_GROUPS: NavGroup[] = [
  {
    labelKey: "nav.about",
    groupPrefix: "/about",
    items: [
      { href: "/about", labelKey: "nav.aboutInstitute" },
      { href: "/about/vision-mission", labelKey: "nav.aboutVisionMission" },
      { href: "/about/president-speech", labelKey: "nav.aboutPresidentSpeech" },
      { href: "/about/director-message", labelKey: "nav.aboutDirectorMessage" },
      { href: "/about/advisory-board", labelKey: "nav.aboutAdvisoryBoard" },
      { href: "/about/team", labelKey: "nav.aboutTeam" },
      { href: "/about/structure", labelKey: "nav.aboutStructure" },
      { href: "/about/partners", labelKey: "nav.aboutPartners" },
    ],
  },
  {
    labelKey: "nav.strategy",
    groupPrefix: "/strategy",
    items: [
      { href: "/strategy/strategy-2025", labelKey: "nav.strategy2025" },
      { href: "/strategy/focus-areas", labelKey: "nav.strategyFocusAreas" },
    ],
  },
  {
    labelKey: "nav.programs",
    groupPrefix: "/programs",
    items: [
      { href: "/programs/urban-policies", labelKey: "nav.programsUrbanPolicies" },
      { href: "/programs/training", labelKey: "nav.programsTraining" },
      { href: "/programs/partnerships", labelKey: "nav.programsPartnerships" },
    ],
  },
  {
    labelKey: "nav.media",
    items: [
      { href: "/media/news", labelKey: "nav.mediaNews" },
      { href: "/media/newsletter", labelKey: "footer.media.newsletter" },
      { href: "/media/city-meetings", labelKey: "footer.media.cityMeetings" },
    ],
  },
];

type Props = {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  arabicHref: AppHref;
  englishHref: AppHref;
  locale: "ar" | "en";
};

export function NavbarMobileMenu({
  open,
  onOpenChange,
  arabicHref,
  englishHref,
  locale,
}: Props) {
  const t = useTranslations("common");
  const pathname = usePathname();
  const [expandedGroup, setExpandedGroup] = useState<string | null>(null);

  useEffect(() => {
    if (!open) {
      setExpandedGroup(null);
      return;
    }

    document.body.style.overflow = "hidden";
    return () => {
      document.body.style.overflow = "";
    };
  }, [open]);

  if (!open) {
    return null;
  }

  return (
    <div className="fixed inset-0 z-60 lg:hidden">
      <button
        type="button"
        className="absolute inset-0 bg-black/50"
        aria-label={t("nav.closeMenu")}
        onClick={() => onOpenChange(false)}
      />

      <div className="absolute inset-x-0 top-0 flex max-h-dvh flex-col bg-background shadow-xl">
        <div className="flex items-center justify-between border-b border-border px-4 py-4">
          <p className="text-base font-semibold text-foreground">{t("nav.menu")}</p>
          <button
            type="button"
            className="inline-flex size-10 items-center justify-center rounded-lg text-foreground hover:bg-muted"
            aria-label={t("nav.closeMenu")}
            onClick={() => onOpenChange(false)}
          >
            <X className="size-5" />
          </button>
        </div>

        <nav
          className="flex-1 overflow-y-auto px-4 py-4"
          aria-label={t("nav.menu")}
        >
          <ul className="space-y-1">
            <li>
              <MobileNavLink
                href="/"
                active={isNavActive(pathname, "/")}
                onNavigate={() => onOpenChange(false)}
              >
                {t("nav.home")}
              </MobileNavLink>
            </li>

            {NAV_GROUPS.map((group) => {
              const isGroupActive =
                (group.groupPrefix
                  ? pathname.startsWith(group.groupPrefix)
                  : false) ||
                group.items.some((item) => isNavActive(pathname, item.href));
              const isExpanded = expandedGroup === group.labelKey;

              return (
                <li key={group.labelKey}>
                  <button
                    type="button"
                    className={cn(
                      "flex w-full items-center justify-between rounded-lg px-3 py-3 text-start text-sm font-medium transition-colors",
                      isGroupActive
                        ? "bg-muted text-primary"
                        : "text-foreground hover:bg-muted/70",
                    )}
                    aria-expanded={isExpanded}
                    onClick={() =>
                      setExpandedGroup(isExpanded ? null : group.labelKey)
                    }
                  >
                    {t(group.labelKey)}
                    <ChevronDown
                      className={cn(
                        "size-4 shrink-0 opacity-60 transition-transform",
                        isExpanded && "rotate-180",
                      )}
                    />
                  </button>

                  {isExpanded ? (
                    <ul className="mt-1 space-y-0.5 border-s-2 border-primary/20 ps-3">
                      {group.items.map((item) => (
                        <li key={hrefPath(item.href)}>
                          <MobileNavLink
                            href={item.href}
                            active={isNavActive(pathname, item.href)}
                            nested
                            onNavigate={() => onOpenChange(false)}
                          >
                            {t(item.labelKey)}
                          </MobileNavLink>
                        </li>
                      ))}
                    </ul>
                  ) : null}
                </li>
              );
            })}

            <li>
              <MobileNavLink
                href="/resources"
                active={isNavActive(pathname, "/resources")}
                onNavigate={() => onOpenChange(false)}
              >
                {t("nav.resources")}
              </MobileNavLink>
            </li>
          </ul>
        </nav>

        <div className="space-y-3 border-t border-border px-4 py-4">
          <div className="grid grid-cols-2 gap-2">
            <Link
              href={arabicHref}
              locale="ar"
              className={cn(
                "rounded-lg border px-3 py-2.5 text-center text-sm font-medium transition-colors",
                locale === "ar"
                  ? "border-primary bg-primary/10 text-primary"
                  : "border-border text-foreground hover:bg-muted",
              )}
              onClick={() => onOpenChange(false)}
            >
              {t("languageSwitcher.ar")}
            </Link>
            <Link
              href={englishHref}
              locale="en"
              className={cn(
                "rounded-lg border px-3 py-2.5 text-center text-sm font-medium transition-colors",
                locale === "en"
                  ? "border-primary bg-primary/10 text-primary"
                  : "border-border text-foreground hover:bg-muted",
              )}
              onClick={() => onOpenChange(false)}
            >
              {t("languageSwitcher.en")}
            </Link>
          </div>

          <ButtonLink
            size="lg"
            className="w-full rounded-lg"
            render={<Link href="/contact" onClick={() => onOpenChange(false)} />}
          >
            <span className="size-2 rounded-full bg-primary-foreground" />
            {t("nav.contact")}
          </ButtonLink>
        </div>
      </div>
    </div>
  );
}

function MobileNavLink({
  href,
  active,
  nested,
  onNavigate,
  children,
}: {
  href: AppHref;
  active: boolean;
  nested?: boolean;
  onNavigate: () => void;
  children: React.ReactNode;
}) {
  return (
    <Link
      href={href}
      onClick={onNavigate}
      className={cn(
        "block rounded-lg px-3 py-3 text-sm transition-colors",
        nested ? "py-2.5" : "font-medium",
        active
          ? "bg-muted font-medium text-primary"
          : "text-foreground hover:bg-muted/70",
      )}
    >
      {children}
    </Link>
  );
}
