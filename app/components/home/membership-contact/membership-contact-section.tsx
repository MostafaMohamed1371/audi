import { MembershipLogo } from "@/app/components/home/membership-contact/membership-logo";
import { ButtonLink } from "@/app/components/ui/button";
import { cn } from "@/lib/utils";
import {
  ChevronLeft,
  Mail,
  Mails,
  Phone,
  Printer,
} from "lucide-react";
import { getLocale, getTranslations } from "next-intl/server";
import Image from "next/image";
import type { ComponentType } from "react";
import { Link } from "@/i18n/routing";
import type { HomePayload } from "@/lib/api";

const contactIcons = {
  phone: Phone,
  fax: Printer,
  email: Mail,
  mailbox: Mails,
} as const;

function ContactRow({
  label,
  value,
  icon: Icon,
  href,
  circled,
  isRtl,
}: {
  label: string;
  value: string;
  icon: ComponentType<{ className?: string; strokeWidth?: number }>;
  href?: string;
  circled?: boolean;
  isRtl: boolean;
}) {
  const row = (
    <div className="flex w-full items-center justify-start gap-3 sm:gap-4">
      <div
        className={cn(
          "flex shrink-0 items-center justify-center text-primary",
          circled ? "size-9 rounded-full border border-primary/35 sm:size-10" : "size-9 sm:size-10",
        )}
      >
        <Icon className="size-4 sm:size-5" strokeWidth={1.5} />
      </div>
      <div className={cn("min-w-0", isRtl ? "text-right" : "text-left")}>
        <p className="text-sm font-medium text-secondary">{label}</p>
        <p className="text-sm text-primary/70">
          <span dir="ltr" className="inline-block w-full text-start">
            {value}
          </span>
        </p>
      </div>
    </div>
  );

  if (href) {
    return (
      <a href={href} className="block transition-opacity hover:opacity-80" >
        {row}
      </a>
    );
  }

  return row;
}

const MAP_EMBED_URL =
  "https://maps.google.com/maps?q=Arab+Urban+Development+Institute,+Riyadh,+Saudi+Arabia&z=15&output=embed";

export async function MembershipContactSection({
  membershipContact,
}: {
  membershipContact?: HomePayload["membershipContact"];
} = {}) {
  const t = await getTranslations("home.membershipContact");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const contactItems = (membershipContact?.contact.items ??
    t.raw("contact.items")) as {
    label: string;
    value: string;
    type: keyof typeof contactIcons;
    href?: string;
  }[];

  const mapEmbedUrl =
    membershipContact?.contact.mapEmbedUrl ?? MAP_EMBED_URL;

  return (
    <section
      id="membership"
      dir={isRtl ? "rtl" : "ltr"}
      className="bg-background py-12 sm:py-16 lg:py-24"
    >
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <div
          className={cn(
            "mb-10 flex flex-col items-center gap-8 sm:mb-12 sm:gap-10 lg:mb-16 lg:flex-row lg:items-center lg:gap-16",
            !isRtl && "lg:flex-row-reverse",
          )}
        >
          <div
            className={cn(
              "flex-1 text-center lg:text-start",
              isRtl ? "lg:text-right" : "lg:text-left",
            )}
          >
            <h2 className="mb-3 text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
              {membershipContact?.membership.title ?? t("membership.title")}
            </h2>
            <p className="mb-6 text-sm leading-7 text-secondary sm:mb-8 sm:text-base sm:leading-8 lg:text-lg">
              {membershipContact?.membership.subtitle ?? t("membership.subtitle")}
            </p>
            <ButtonLink
              size="lg"
              className="w-full rounded-full bg-primary px-8 hover:bg-primary/90 sm:w-auto"
              render={
                <Link
                  href={
                    membershipContact?.membership.href?.includes("#membership")
                      ? { pathname: "/contact", hash: "membership" }
                      : ((membershipContact?.membership.href ??
                          t("membership.href")) as "/contact")
                  }
                />
              }
            >
              {membershipContact?.membership.cta ?? t("membership.cta")}
              <ChevronLeft className="size-4" />
            </ButtonLink>
          </div>

          <div className="shrink-0">
            <MembershipLogo className="h-32 w-32 sm:h-48 sm:w-48 lg:h-52 lg:w-52" />
          </div>
        </div>

        <article className="overflow-hidden rounded-[24px] bg-white shadow-[1px_1px_18.6px_0px_#111F421C] sm:rounded-[30px]">
          <div className="grid lg:grid-cols-[minmax(130px,0.75fr)_minmax(0,1.35fr)_minmax(0,1.5fr)]">
            <div
              className={cn(
                "flex items-center justify-center border-b border-border/70 px-5 py-8 sm:px-6 sm:py-10 lg:border-b-0 lg:py-14",
                isRtl ? "lg:border-e" : "lg:border-s",
              )}
            >
              <h3 className="text-center text-lg font-bold text-secondary sm:text-2xl">
                {membershipContact?.contact.title ?? t("contact.title")}
              </h3>
            </div>

            <div
              className={cn(
                "flex flex-col items-start justify-center gap-5 border-b border-border/70 px-5 py-8 sm:gap-7 sm:px-8 sm:py-10 lg:gap-8 lg:border-b-0 lg:px-10 lg:py-14",
                isRtl ? "lg:border-e" : "lg:border-s",
              )}
            >
              {contactItems.map((item) => (
                <ContactRow
                  key={item.label}
                  label={item.label}
                  value={item.value}
                  icon={contactIcons[item.type]}
                  href={item.href}
                  circled={item.type === "phone"}
                  isRtl={isRtl}
                />
              ))}
            </div>

            <div className="flex min-h-0 flex-col sm:min-h-[280px] lg:min-h-[360px]">
              <div
                className={cn(
                  "px-5 pt-6 pb-4 sm:px-8 sm:pt-8 sm:pb-5 lg:px-10 lg:pt-10",
                  isRtl ? "text-right" : "text-left",
                )}
              >
                <div
                  dir="ltr"
                  className="grid grid-cols-[1fr_auto] items-center gap-3"
                >
                  <div
                    className={cn(
                      "min-w-0",
                      isRtl ? "text-right" : "text-left",
                    )}
                  >
                    <p className="mb-1 text-sm font-bold text-secondary">
                      {membershipContact?.contact.addressTitle ??
                        t("contact.addressTitle")}
                    </p>
                    <p className="text-sm leading-7 text-primary">
                      {membershipContact?.contact.address ?? t("contact.address")}
                    </p>
                  </div>
                  <Image
                    src="/icons/map.png"
                    alt=""
                    width={30}
                    height={30}
                    className="shrink-0 object-contain"
                  />
                </div>
              </div>

              <div className="relative min-h-[200px] flex-1 sm:min-h-[220px] lg:min-h-[260px]">
                <iframe
                  title={
                    membershipContact?.contact.mapTitle ?? t("contact.mapTitle")
                  }
                  src={mapEmbedUrl}
                  className="absolute inset-0 h-full w-full border-0"
                  loading="lazy"
                  referrerPolicy="no-referrer-when-downgrade"
                  allowFullScreen
                />
              </div>
            </div>
          </div>
        </article>
      </div>
    </section>
  );
}
