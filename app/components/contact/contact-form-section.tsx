"use client";

import { Button } from "@/app/components/ui/button";
import { submitContactForm } from "@/lib/api";
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

export function ContactFormSection({ isRtl }: Props) {
  const t = useTranslations("contact.form");
  const locale = useLocale();
  const [name, setName] = useState("");
  const [phone, setPhone] = useState("");
  const [email, setEmail] = useState("");
  const [message, setMessage] = useState("");
  const [status, setStatus] = useState<"idle" | "success" | "error">("idle");
  const [isSubmitting, setIsSubmitting] = useState(false);

  async function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setIsSubmitting(true);
    setStatus("idle");

    try {
      await submitContactForm({ name, phone, email, message }, locale);
      setStatus("success");
      setName("");
      setPhone("");
      setEmail("");
      setMessage("");
    } catch {
      setStatus("error");
    } finally {
      setIsSubmitting(false);
    }
  }

  return (
    <section
      dir={isRtl ? "rtl" : "ltr"}
      className="bg-linear-to-b from-[#E8F4FA] via-[#F4F9FC] to-white py-16 sm:py-20 lg:py-24"
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
                id="contact-name"
                label={t("fields.name.label")}
                placeholder={t("fields.name.placeholder")}
                value={name}
                onChange={setName}
                required
              />

              <div className="grid gap-8 sm:grid-cols-2">
                <UnderlineField
                  id="contact-phone"
                  label={t("fields.phone.label")}
                  placeholder={t("fields.phone.placeholder")}
                  type="tel"
                  value={phone}
                  onChange={setPhone}
                  required
                />
                <UnderlineField
                  id="contact-email"
                  label={t("fields.email.label")}
                  placeholder={t("fields.email.placeholder")}
                  type="email"
                  value={email}
                  onChange={setEmail}
                  required
                />
              </div>

              <UnderlineField
                id="contact-message"
                label={t("fields.message.label")}
                placeholder={t("fields.message.placeholder")}
                multiline
                value={message}
                onChange={setMessage}
                required
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
