import { cn } from "@/lib/utils";
import { Mail, MapPin, Phone, Printer } from "lucide-react";
import type { ComponentType } from "react";

const contactIcons = {
  phone: Phone,
  fax: Printer,
  email: Mail,
} as const;

const MAP_EMBED_URL =
  "https://maps.google.com/maps?q=Arab+Urban+Development+Institute,+Riyadh,+Saudi+Arabia&z=15&output=embed";

type ContactItem = {
  label: string;
  value: string;
  type: keyof typeof contactIcons;
  href?: string;
};

type Props = {
  title: string;
  subtitle: string;
  addressLabel: string;
  address: string;
  mapTitle: string;
  mapEmbedUrl?: string;
  items: ContactItem[];
  isRtl: boolean;
};

function ContactInfoCard({
  label,
  value,
  icon: Icon,
  href,
}: {
  label: string;
  value: string;
  icon: ComponentType<{ className?: string; strokeWidth?: number }>;
  href?: string;
}) {
  const content = (
    <div className="flex flex-col items-center gap-3 text-center">
      <div className="flex size-12 items-center justify-center text-primary">
        <Icon className="size-7" strokeWidth={1.5} />
      </div>
      <div>
        <p className="text-sm font-medium text-secondary">{label}</p>
        <p className="mt-1 text-sm text-primary/80">
          <span dir="ltr" className="inline-block">
            {value}
          </span>
        </p>
      </div>
    </div>
  );

  if (href) {
    return (
      <a href={href} className="block transition-opacity hover:opacity-80">
        {content}
      </a>
    );
  }

  return content;
}

export function ContactInfoSection({
  title,
  subtitle,
  addressLabel,
  address,
  mapTitle,
  mapEmbedUrl,
  items,
  isRtl,
}: Props) {
  return (
    <section
      dir={isRtl ? "rtl" : "ltr"}
      className="bg-white pt-10 pb-16 sm:pt-14 sm:pb-20 lg:pt-16 lg:pb-24"
    >
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <div
          className={cn(
            "flex flex-col gap-10 lg:flex-row lg:items-start lg:gap-14 xl:gap-20",
            !isRtl && "lg:flex-row-reverse",
          )}
        >
          <div className="flex-1 space-y-8">
            <div className={cn(isRtl ? "text-right" : "text-left")}>
              <h1 className="text-3xl font-bold text-secondary sm:text-4xl lg:text-[2.5rem]">
                {title}
              </h1>
              <p className="mt-3 max-w-xl text-base leading-8 text-muted-foreground sm:text-lg">
                {subtitle}
              </p>
            </div>

            <div className="grid gap-8 sm:grid-cols-3">
              {items.map((item) => (
                <ContactInfoCard
                  key={item.label}
                  label={item.label}
                  value={item.value}
                  icon={contactIcons[item.type]}
                  href={item.href}
                />
              ))}
            </div>

            <div
              className={cn(
                "flex items-start gap-3 border-t border-border/50 pt-8",
                isRtl ? "flex-row-reverse text-right" : "text-left",
              )}
            >
              <MapPin
                className="mt-0.5 size-6 shrink-0 text-primary"
                strokeWidth={1.5}
              />
              <div>
                <p className="text-sm font-bold text-secondary">{addressLabel}</p>
                <p className="mt-1 text-sm leading-7 text-primary/80">{address}</p>
              </div>
            </div>
          </div>

          <div className="w-full shrink-0 lg:w-[min(100%,420px)] xl:w-[min(100%,480px)]">
            <div className="relative aspect-square overflow-hidden rounded-3xl shadow-[1px_1px_18.6px_0px_#111F421C]">
              <iframe
                title={mapTitle}
                src={mapEmbedUrl ?? MAP_EMBED_URL}
                className="absolute inset-0 h-full w-full border-0"
                loading="lazy"
                referrerPolicy="no-referrer-when-downgrade"
                allowFullScreen
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
