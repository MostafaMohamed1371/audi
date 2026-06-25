"use client";

import { ButtonLink } from "@/app/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/app/components/ui/dropdown-menu";
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
  navigationMenuTriggerStyle,
} from "@/app/components/ui/navigation-menu";
import { Link, usePathname, type AppHref } from "@/i18n/routing";
import { resolveMediaLocaleSwitchHref } from "@/lib/media-slugs";
import { isInternalHeroPage } from "@/lib/internal-pages";
import { cn } from "@/lib/utils";
import { ChevronDown, Menu } from "lucide-react";
import { useLocale, useTranslations } from "next-intl";
import Image from "next/image";
import { useParams, useSearchParams } from "next/navigation";
import { useEffect, useState } from "react";
import { NavbarMobileMenu } from "@/app/components/layout/navbar-mobile-menu";

function hrefPath(href: AppHref) {
  return typeof href === "string" ? href.split("#")[0] : href.pathname;
}

function isNavActive(pathname: string, href: AppHref) {
  const path = hrefPath(href);
  if (path === "/") return pathname === "/";
  return pathname === path || pathname.startsWith(`${path}/`);
}

function navTriggerClass(overlay: boolean, active?: boolean) {
  return cn(
    navigationMenuTriggerStyle(),
    overlay
      ? "text-white hover:!bg-white hover:text-secondary focus:!bg-white focus:text-secondary data-open:!bg-white data-open:text-secondary data-open:hover:!bg-white data-open:hover:text-secondary data-popup-open:!bg-white data-popup-open:text-secondary data-popup-open:hover:!bg-white data-popup-open:hover:text-secondary"
      : active && "bg-muted text-primary hover:bg-muted",
    overlay &&
      active &&
      "bg-white/20 font-medium text-white hover:!bg-white hover:text-secondary data-open:!bg-white data-open:text-secondary",
  );
}

export function Navbar() {
  const t = useTranslations("common");
  const locale = useLocale() as "ar" | "en";
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const routeParams = useParams();
  const routeSlug =
    typeof routeParams.slug === "string" ? routeParams.slug : undefined;
  const hasProgramTab =
    (pathname === "/programs/training" ||
      pathname === "/programs/partnerships") &&
    searchParams.has("tab");
  const overlay = isInternalHeroPage(pathname) && !hasProgramTab;
  const [isScrolled, setIsScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const heroOverlay = overlay && !isScrolled;

  useEffect(() => {
    if (!overlay) {
      setIsScrolled(false);
      return;
    }

    const onScroll = () => setIsScrolled(window.scrollY > 24);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, [overlay, pathname]);

  const arabicHref =
    resolveMediaLocaleSwitchHref(pathname, locale, "ar", routeSlug) ??
    (pathname as AppHref);
  const englishHref =
    resolveMediaLocaleSwitchHref(pathname, locale, "en", routeSlug) ??
    (pathname as AppHref);

  return (
    <header
      className={cn(
        "top-0 z-50 w-full transition-colors duration-300",
        overlay
          ? cn(
              "fixed",
              heroOverlay
                ? "border-transparent bg-transparent text-white"
                : "border-b border-border bg-background text-foreground shadow-sm",
            )
          : "sticky border-b border-border bg-background",
      )}
    >
      <nav className="mx-auto flex h-20 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:gap-8">
        <Link href="/" className="shrink-0">
          <Image
            src="/logo.png"
            alt={t("siteName")}
            width={140}
            height={72}
            className={cn(
              "h-14 w-auto object-contain transition-[filter] duration-300",
              heroOverlay && "brightness-0 invert",
            )}
            priority
          />
        </Link>

        <NavigationMenu className="hidden max-w-none flex-1 justify-center lg:flex">
          <NavigationMenuList className="gap-1">
            <NavigationMenuItem>
              <NavigationMenuLink
                className={navTriggerClass(heroOverlay, isNavActive(pathname, "/"))}
                render={<Link href="/" />}
              >
                {t("nav.home")}
              </NavigationMenuLink>
            </NavigationMenuItem>

            <NavDropdown
              overlay={heroOverlay}
              pathname={pathname}
              label={t("nav.about")}
              groupPrefix="/about"
              items={[
                { href: "/about", label: t("nav.aboutInstitute") },
                {
                  href: "/about/vision-mission",
                  label: t("nav.aboutVisionMission"),
                },
                {
                  href: "/about/president-speech",
                  label: t("nav.aboutPresidentSpeech"),
                },
                {
                  href: "/about/director-message",
                  label: t("nav.aboutDirectorMessage"),
                },
                {
                  href: "/about/advisory-board",
                  label: t("nav.aboutAdvisoryBoard"),
                },
                { href: "/about/team", label: t("nav.aboutTeam") },
                { href: "/about/structure", label: t("nav.aboutStructure") },
                { href: "/about/partners", label: t("nav.aboutPartners") },
              ]}
            />

            <NavDropdown
              overlay={heroOverlay}
              pathname={pathname}
              label={t("nav.strategy")}
              groupPrefix="/strategy"
              items={[
                {
                  href: "/strategy/strategy-2025",
                  label: t("nav.strategy2025"),
                },
                {
                  href: "/strategy/focus-areas",
                  label: t("nav.strategyFocusAreas"),
                },
              ]}
            />

            <NavDropdown
              overlay={heroOverlay}
              pathname={pathname}
              label={t("nav.programs")}
              groupPrefix="/programs"
              items={[
                {
                  href: "/programs/urban-policies",
                  label: t("nav.programsUrbanPolicies"),
                },
                {
                  href: "/programs/training",
                  label: t("nav.programsTraining"),
                },
                {
                  href: "/programs/partnerships",
                  label: t("nav.programsPartnerships"),
                },
              ]}
            />

            <NavigationMenuItem>
              <NavigationMenuLink
                className={navTriggerClass(heroOverlay)}
                render={<Link href="/resources" />}
              >
                {t("nav.resources")}
              </NavigationMenuLink>
            </NavigationMenuItem>

            <NavDropdown
              overlay={heroOverlay}
              pathname={pathname}
              label={t("nav.media")}
              items={[
                { href: "/media/news", label: t("nav.mediaNews") },
                { href: "/media/newsletter", label: t("footer.media.newsletter") },
                {
                  href: "/media/city-meetings",
                  label: t("footer.media.cityMeetings"),
                },
              ]}
            />
          </NavigationMenuList>
        </NavigationMenu>

        <div className="ms-auto flex shrink-0 items-center gap-1.5 sm:gap-3">
          <button
            type="button"
            className={cn(
              "inline-flex size-10 items-center justify-center rounded-lg lg:hidden",
              heroOverlay
                ? "text-white hover:bg-white/10"
                : "text-foreground hover:bg-muted",
            )}
            aria-label={t("nav.openMenu")}
            aria-expanded={mobileOpen}
            onClick={() => setMobileOpen(true)}
          >
            <Menu className="size-6" />
          </button>

          <DropdownMenu>
            <DropdownMenuTrigger
              className={cn(
                navTriggerClass(heroOverlay),
                !heroOverlay && "data-popup-open:bg-muted",
                "hidden sm:inline-flex",
              )}
            >
              {t("languageSwitcher.label")}
              <ChevronDown className="size-3.5 opacity-60" />
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuItem render={<Link href={arabicHref} locale="ar" />}>
                {t("languageSwitcher.ar")}
              </DropdownMenuItem>
              <DropdownMenuItem render={<Link href={englishHref} locale="en" />}>
                {t("languageSwitcher.en")}
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>

          <ButtonLink
            size="lg"
            variant={heroOverlay ? "secondary" : "default"}
            className={cn(
              "hidden rounded-lg px-4 sm:inline-flex",
              heroOverlay &&
                "border-white/20 bg-secondary/90 text-white hover:bg-secondary",
            )}
            render={<Link href="/contact" />}
          >
            <span
              className={cn(
                "size-2 rounded-full",
                heroOverlay ? "bg-white" : "bg-primary-foreground",
              )}
            />
            {t("nav.contact")}
          </ButtonLink>
        </div>
      </nav>

      <NavbarMobileMenu
        open={mobileOpen}
        onOpenChange={setMobileOpen}
        arabicHref={arabicHref}
        englishHref={englishHref}
        locale={locale}
      />
    </header>
  );
}

function NavDropdown({
  overlay,
  pathname,
  label,
  items,
  groupPrefix,
}: {
  overlay: boolean;
  pathname: string;
  label: string;
  items: { href: AppHref; label: string }[];
  groupPrefix?: string;
}) {
  const isGroupActive =
    (groupPrefix ? pathname.startsWith(groupPrefix) : false) ||
    items.some((item) => isNavActive(pathname, item.href));

  return (
    <NavigationMenuItem>
      <NavigationMenuTrigger
        className={navTriggerClass(overlay, isGroupActive)}
      >
        {label}
      </NavigationMenuTrigger>
      <NavigationMenuContent>
        <ul className="w-72 min-w-max p-2">
          {items.map((item, index) => {
            const active = isNavActive(pathname, item.href);

            return (
              <li key={hrefPath(item.href)}>
                {index > 0 ? (
                  <div className="my-1 border-t border-border/60" aria-hidden />
                ) : null}
                <NavigationMenuLink
                  className={cn(
                    "block rounded-md px-3 py-2.5 text-sm leading-6",
                    active &&
                      "bg-muted font-medium text-primary hover:bg-muted",
                  )}
                  data-active={active ? "" : undefined}
                  render={<Link href={item.href} />}
                >
                  {item.label}
                </NavigationMenuLink>
              </li>
            );
          })}
        </ul>
      </NavigationMenuContent>
    </NavigationMenuItem>
  );
}
