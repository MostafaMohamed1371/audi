"use client";

import { Button } from "@/app/components/ui/button";
import { submitJobApplication } from "@/lib/api";
import { cn } from "@/lib/utils";
import { Send } from "lucide-react";
import { useLocale, useTranslations } from "next-intl";
import { useState } from "react";

type Props = {
  jobOpeningId: number;
  isRtl: boolean;
};

function Field({
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

export function JobApplicationForm({ jobOpeningId, isRtl }: Props) {
  const t = useTranslations("careers.application");
  const locale = useLocale();
  const [fullName, setFullName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [coverLetter, setCoverLetter] = useState("");
  const [cvUrl, setCvUrl] = useState("");
  const [status, setStatus] = useState<"idle" | "success" | "error">("idle");
  const [isSubmitting, setIsSubmitting] = useState(false);

  async function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setIsSubmitting(true);
    setStatus("idle");

    try {
      await submitJobApplication(
        {
          jobOpeningId,
          fullName,
          email,
          phone: phone || undefined,
          coverLetter: coverLetter || undefined,
          cvUrl: cvUrl || undefined,
        },
        locale,
      );
      setStatus("success");
      setFullName("");
      setEmail("");
      setPhone("");
      setCoverLetter("");
      setCvUrl("");
    } catch {
      setStatus("error");
    } finally {
      setIsSubmitting(false);
    }
  }

  return (
    <section
      dir={isRtl ? "rtl" : "ltr"}
      className="mt-12 rounded-2xl border border-border/60 bg-[#F4F9FC] p-6 sm:p-8"
    >
      <div className="mb-8 space-y-2">
        <h2 className="text-xl font-bold text-secondary sm:text-2xl">
          {t("title")}
        </h2>
        <p className="text-sm leading-7 text-muted-foreground sm:text-base">
          {t("description")}
        </p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        <Field
          id="job-full-name"
          label={t("fields.fullName.label")}
          placeholder={t("fields.fullName.placeholder")}
          value={fullName}
          onChange={setFullName}
          required
        />

        <div className="grid gap-6 sm:grid-cols-2">
          <Field
            id="job-email"
            label={t("fields.email.label")}
            placeholder={t("fields.email.placeholder")}
            type="email"
            value={email}
            onChange={setEmail}
            required
          />
          <Field
            id="job-phone"
            label={t("fields.phone.label")}
            placeholder={t("fields.phone.placeholder")}
            type="tel"
            value={phone}
            onChange={setPhone}
          />
        </div>

        <Field
          id="job-cover-letter"
          label={t("fields.coverLetter.label")}
          placeholder={t("fields.coverLetter.placeholder")}
          multiline
          value={coverLetter}
          onChange={setCoverLetter}
        />

        <Field
          id="job-cv-url"
          label={t("fields.cvUrl.label")}
          placeholder={t("fields.cvUrl.placeholder")}
          type="url"
          value={cvUrl}
          onChange={setCvUrl}
        />

        {status === "success" ? (
          <p className="text-sm font-medium text-primary">{t("success")}</p>
        ) : null}
        {status === "error" ? (
          <p className="text-sm font-medium text-destructive">{t("error")}</p>
        ) : null}

        <Button
          type="submit"
          size="lg"
          disabled={isSubmitting}
          className="h-12 rounded-full bg-primary px-10 text-base hover:bg-primary/90"
        >
          {t("submit")}
          <Send className={cn("size-4", isRtl && "rotate-180")} aria-hidden />
        </Button>
      </form>
    </section>
  );
}
