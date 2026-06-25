"use client";

import { Button } from "@/app/components/ui/button";
import { submitMembershipApplication } from "@/lib/api";
import { cn } from "@/lib/utils";
import { Send } from "lucide-react";
import { useLocale, useTranslations } from "next-intl";
import { useState } from "react";

type Props = {
  isRtl: boolean;
};

function UnderlineField({
  id,
  label,
  placeholder,
  type = "text",
  multiline = false,
  value,
  onChange,
  required,
}: {
  id: string;
  label: string;
  placeholder: string;
  type?: string;
  multiline?: boolean;
  value: string;
  onChange: (value: string) => void;
  required?: boolean;
}) {
  const sharedClassName =
    "w-full border-0 border-b border-border bg-transparent py-2 text-sm text-secondary outline-none transition-colors placeholder:text-muted-foreground/60 focus:border-primary";

  return (
    <div className="space-y-2">
      <label htmlFor={id} className="text-sm font-medium text-secondary">
        {label}
      </label>
      {multiline ? (
        <textarea
          id={id}
          rows={4}
          placeholder={placeholder}
          value={value}
          onChange={(event) => onChange(event.target.value)}
          required={required}
          className={cn(sharedClassName, "resize-none")}
        />
      ) : (
        <input
          id={id}
          type={type}
          placeholder={placeholder}
          value={value}
          onChange={(event) => onChange(event.target.value)}
          required={required}
          className={sharedClassName}
        />
      )}
    </div>
  );
}

export function MembershipFormSection({ isRtl }: Props) {
  const t = useTranslations("contact.membership");
  const locale = useLocale();
  const [organizationName, setOrganizationName] = useState("");
  const [contactName, setContactName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [countryCode, setCountryCode] = useState("");
  const [city, setCity] = useState("");
  const [message, setMessage] = useState("");
  const [status, setStatus] = useState<"idle" | "success" | "error">("idle");
  const [isSubmitting, setIsSubmitting] = useState(false);

  async function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setIsSubmitting(true);
    setStatus("idle");

    try {
      await submitMembershipApplication(
        {
          organizationName,
          contactName,
          email,
          phone,
          countryCode: countryCode || undefined,
          city: city || undefined,
          message: message || undefined,
        },
        locale,
      );
      setStatus("success");
      setOrganizationName("");
      setContactName("");
      setEmail("");
      setPhone("");
      setCountryCode("");
      setCity("");
      setMessage("");
    } catch {
      setStatus("error");
    } finally {
      setIsSubmitting(false);
    }
  }

  return (
    <section
      id="membership"
      dir={isRtl ? "rtl" : "ltr"}
      className="bg-background py-16 sm:py-20 lg:py-24"
    >
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <div
          className={cn(
            "flex flex-col gap-12 lg:flex-row lg:items-start lg:gap-16 xl:gap-24",
            !isRtl && "lg:flex-row-reverse",
          )}
        >
          <div
            className={cn(
              "shrink-0 lg:w-[min(100%,340px)]",
              isRtl ? "text-right" : "text-left",
            )}
          >
            <h2 className="text-2xl font-bold text-primary sm:text-3xl">
              {t("title")}
            </h2>
            <p className="mt-4 text-base leading-8 text-muted-foreground">
              {t("description")}
            </p>
          </div>

          <div className="min-w-0 flex-1">
            <form onSubmit={handleSubmit} className="space-y-8">
              <UnderlineField
                id="membership-organization"
                label={t("fields.organizationName.label")}
                placeholder={t("fields.organizationName.placeholder")}
                value={organizationName}
                onChange={setOrganizationName}
                required
              />

              <UnderlineField
                id="membership-contact-name"
                label={t("fields.contactName.label")}
                placeholder={t("fields.contactName.placeholder")}
                value={contactName}
                onChange={setContactName}
                required
              />

              <div className="grid gap-8 sm:grid-cols-2">
                <UnderlineField
                  id="membership-email"
                  label={t("fields.email.label")}
                  placeholder={t("fields.email.placeholder")}
                  type="email"
                  value={email}
                  onChange={setEmail}
                  required
                />
                <UnderlineField
                  id="membership-phone"
                  label={t("fields.phone.label")}
                  placeholder={t("fields.phone.placeholder")}
                  type="tel"
                  value={phone}
                  onChange={setPhone}
                  required
                />
              </div>

              <div className="grid gap-8 sm:grid-cols-2">
                <UnderlineField
                  id="membership-country"
                  label={t("fields.countryCode.label")}
                  placeholder={t("fields.countryCode.placeholder")}
                  value={countryCode}
                  onChange={setCountryCode}
                />
                <UnderlineField
                  id="membership-city"
                  label={t("fields.city.label")}
                  placeholder={t("fields.city.placeholder")}
                  value={city}
                  onChange={setCity}
                />
              </div>

              <UnderlineField
                id="membership-message"
                label={t("fields.message.label")}
                placeholder={t("fields.message.placeholder")}
                multiline
                value={message}
                onChange={setMessage}
              />

              {status === "success" ? (
                <p className="text-sm font-medium text-primary">{t("success")}</p>
              ) : null}
              {status === "error" ? (
                <p className="text-sm font-medium text-destructive">{t("error")}</p>
              ) : null}

              <div className="flex justify-center pt-2">
                <Button
                  type="submit"
                  size="lg"
                  disabled={isSubmitting}
                  className="h-12 min-w-[200px] rounded-full bg-primary px-10 text-base hover:bg-primary/90"
                >
                  {t("submit")}
                  <Send
                    className={cn("size-4", isRtl && "rotate-180")}
                    aria-hidden
                  />
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  );
}
