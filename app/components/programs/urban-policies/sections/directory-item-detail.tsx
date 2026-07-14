"use client";

import {
  fetchDirectoryItem,
  submitDirectoryDiscussion,
  type DirectoryItemDetail,
  type DirectoryTab,
} from "@/lib/api";
import { getFallbackDirectoryItem } from "@/lib/directory-item-fallback";
import { cn } from "@/lib/utils";
import { Download, Share2, X } from "lucide-react";
import { useLocale } from "next-intl";
import Image from "next/image";
import { useCallback, useEffect, useState, type ReactNode } from "react";
import type { PortalDirectoryPublicationFields } from "@/app/components/programs/urban-policies/shared/types";

type DetailSection = {
  title?: string;
  paragraphs?: string[];
  bullets?: string[];
  image?: string;
  figures?: {
    image: string;
    caption?: string;
    address?: string;
    source?: string;
  }[];
};

type RelatedProject = {
  city: string;
  country: string;
  dateRange: string;
  image?: string;
  href?: string;
};

type DetailContent = {
  layout?: "simple" | "rich";
  title?: string;
  country?: string;
  population?: string;
  paragraphs?: string[];
  image?: string;
  sections?: DetailSection[];
  relatedProjects?: RelatedProject[];
  cta?: {
    title?: string;
    description?: string;
    button?: string;
    href?: string;
  };
};

type Props = {
  tab: DirectoryTab;
  number: string;
  isRtl: boolean;
  fallbackUi: {
    discussionTitle?: string;
    addCommentLabel?: string;
    authorNameLabel?: string;
    commentBodyLabel?: string;
    submitCommentLabel?: string;
    backToListLabel?: string;
    commentSuccess?: string;
    commentError?: string;
    shareLabel?: string;
    downloadLabel?: string;
    addressLabel?: string;
    sourceLabel?: string;
    relatedProjectsTitle?: string;
    organizationFields?: Record<string, string>;
    publicationFields?: Partial<PortalDirectoryPublicationFields>;
    projectDescriptionTitle?: string;
    projectValuesTitle?: string;
    projectPolicyToolsTitle?: string;
    viewSourceLabel?: string;
    foundersTitle?: string;
    referencesAccordionTitle?: string;
    projectLinkLabel?: string;
    notesLabel?: string;
    referencesLabel?: string;
    directoryCta?: {
      title?: string;
      description?: string;
      button?: string;
      href?: string;
    };
  };
  initialData?: DirectoryItemDetail | null;
  onBack: () => void;
};

function itemTitle(item: Record<string, unknown>, detail: DetailContent): string {
  if (typeof detail.title === "string") return detail.title;
  if (typeof item.title === "string") return item.title;
  if (typeof item.city === "string" && typeof item.country === "string") {
    return `${item.city}, ${item.country}`;
  }
  if (typeof item.name === "string") return item.name.split("،")[0]?.split(",")[0]?.trim() ?? item.name;
  return String(item.number ?? "");
}

function RelatedProjectCard({
  project,
  isRtl,
}: {
  project: RelatedProject;
  isRtl: boolean;
}) {
  const content = (
    <>
      {project.image ? (
        <div className="relative mb-3 aspect-4/3 overflow-hidden rounded-xl">
          <Image
            src={project.image}
            alt={`${project.city}, ${project.country}`}
            fill
            className="object-cover"
            sizes="(max-width: 768px) 100vw, 240px"
          />
        </div>
      ) : null}
      <p className="text-sm font-bold text-secondary">
        {project.city}, {project.country}
      </p>
      <p className="mt-1 text-xs text-primary">{project.dateRange}</p>
    </>
  );

  if (project.href) {
    return (
      <a
        href={project.href}
        className="block rounded-xl bg-[#eef6fa] p-4 transition-colors hover:bg-[#e3f1f7]"
        dir={isRtl ? "rtl" : "ltr"}
      >
        {content}
      </a>
    );
  }

  return (
    <article className="rounded-xl bg-[#eef6fa] p-4" dir={isRtl ? "rtl" : "ltr"}>
      {content}
    </article>
  );
}

type OrganizationProfile = {
  type?: string;
  country?: string;
  countryCode?: string;
  address?: string;
  phone?: string;
  email?: string;
  website?: string;
  founded?: string;
  employees?: string;
  budget?: string;
  interventionAreas?: string;
  interventionFields?: string[];
  interventionTypes?: string[];
  socialLinks?: { platform: string; href: string }[];
};

function organizationProfileFromItem(
  item: Record<string, unknown>,
  detail: Record<string, unknown>,
): OrganizationProfile {
  const merged = { ...detail, ...item } as OrganizationProfile;
  const nestedDetail = item.detail;
  if (nestedDetail && typeof nestedDetail === "object") {
    return { ...merged, ...(nestedDetail as OrganizationProfile) };
  }
  return merged;
}

function OrganizationCountryFlag({ code }: { code?: string }) {
  if (!code) {
    return null;
  }

  return (
    // eslint-disable-next-line @next/next/no-img-element
    <img
      src={`https://flagcdn.com/w20/${code.toLowerCase()}.png`}
      alt=""
      width={20}
      height={15}
      className="inline-block size-5 rounded-sm object-cover"
    />
  );
}

function OrganizationProfileSection({
  profile,
  labels,
  isRtl,
}: {
  profile: OrganizationProfile;
  labels: Record<string, string>;
  isRtl: boolean;
}) {
  const scalarFields: (keyof OrganizationProfile)[] = [
    "address",
    "phone",
    "email",
    "website",
    "type",
    "founded",
    "employees",
    "budget",
    "interventionAreas",
  ];

  return (
    <div className="space-y-8">
      <div className="grid gap-4 sm:grid-cols-2">
        {scalarFields.map((field) => {
          const value = profile[field];
          if (typeof value !== "string" || value === "") {
            return null;
          }

          const label = labels[field] ?? field;
          const content =
            field === "website" || field === "email" ? (
              <a
                href={field === "email" ? `mailto:${value}` : value}
                className="text-primary hover:underline"
                dir="ltr"
              >
                {value}
              </a>
            ) : (
              <span>{value}</span>
            );

          return (
            <div
              key={field}
              className="rounded-xl border border-[#00709E14] bg-[#eef6fa] p-4"
            >
              <p className="mb-1 text-xs font-bold uppercase tracking-wide text-primary">
                {label}
              </p>
              <p className="text-sm leading-7 text-secondary sm:text-base">{content}</p>
            </div>
          );
        })}
      </div>

      {profile.interventionFields && profile.interventionFields.length > 0 ? (
        <div>
          <h2 className="mb-4 text-xl font-bold text-secondary sm:text-2xl">
            {labels.interventionFields ?? "Intervention Fields"}
          </h2>
          <ul
            className={cn(
              "list-disc space-y-2 ps-5 text-base leading-8 text-[#4d5a6f]",
              isRtl ? "text-right" : "text-left",
            )}
          >
            {profile.interventionFields.map((entry) => (
              <li key={entry}>{entry}</li>
            ))}
          </ul>
        </div>
      ) : null}

      {profile.interventionTypes && profile.interventionTypes.length > 0 ? (
        <div>
          <h2 className="mb-4 text-xl font-bold text-secondary sm:text-2xl">
            {labels.interventionTypes ?? "Intervention Types"}
          </h2>
          <ul
            className={cn(
              "list-disc space-y-2 ps-5 text-base leading-8 text-[#4d5a6f]",
              isRtl ? "text-right" : "text-left",
            )}
          >
            {profile.interventionTypes.map((entry) => (
              <li key={entry}>{entry}</li>
            ))}
          </ul>
        </div>
      ) : null}

      {profile.socialLinks && profile.socialLinks.length > 0 ? (
        <div className="flex flex-wrap gap-3">
          {profile.socialLinks.map((link) => (
            <a
              key={link.href}
              href={link.href}
              target="_blank"
              rel="noopener noreferrer"
              className="rounded-lg border border-[#00709E26] px-4 py-2 text-sm font-medium text-primary hover:bg-[#f4f6f8]"
            >
              {link.platform}
            </a>
          ))}
        </div>
      ) : null}
    </div>
  );
}

type PublicationLanguage = { code: string; label: string };

type PublicationProfile = {
  name?: string;
  organizationName?: string;
  organizationType?: string;
  publicationCountry?: string;
  languages?: PublicationLanguage[];
  publicationDate?: string;
  publicationType?: string;
  topics?: string[];
  publicationLink?: string;
  coverImage?: string;
  languageVersions?: {
    ar?: { label: string; href?: string };
    en?: { label: string; href?: string };
  };
};

function publicationProfileFromItem(
  item: Record<string, unknown>,
  detail: Record<string, unknown>,
): PublicationProfile {
  const merged = { ...detail, ...item } as PublicationProfile;
  const nestedDetail = item.detail;
  if (nestedDetail && typeof nestedDetail === "object") {
    return { ...merged, ...(nestedDetail as PublicationProfile) };
  }
  return merged;
}

function PublicationProfileSection({
  profile,
  labels,
  isRtl,
  onClose,
}: {
  profile: PublicationProfile;
  labels: Partial<PortalDirectoryPublicationFields>;
  isRtl: boolean;
  onClose: () => void;
}) {
  const title = profile.name ?? "";
  const languageVersionAr = profile.languageVersions?.ar;
  const languageVersionEn = profile.languageVersions?.en;

  const fieldRows: { key: keyof PortalDirectoryPublicationFields; content: ReactNode }[] = [
    {
      key: "organizationName",
      content: profile.organizationName ? (
        <p className="text-base font-bold text-secondary sm:text-lg">{profile.organizationName}</p>
      ) : null,
    },
    {
      key: "organizationType",
      content: profile.organizationType ? (
        <p className="rounded-lg bg-[#eef6fa] px-3 py-2 text-sm leading-7 text-secondary sm:text-base">
          {profile.organizationType}
        </p>
      ) : null,
    },
    {
      key: "publicationCountry",
      content:
        profile.publicationCountry && profile.publicationCountry.trim() !== "" ? (
          <p className="text-sm leading-7 text-secondary sm:text-base">
            {profile.publicationCountry}
          </p>
        ) : null,
    },
    {
      key: "languages",
      content:
        profile.languages && profile.languages.length > 0 ? (
          <div className="flex flex-wrap gap-2">
            {profile.languages.map((language) => (
              <span
                key={language.code}
                className="rounded-full border border-primary bg-[#eef6fa] px-4 py-1.5 text-sm font-medium text-primary"
              >
                {language.label}
              </span>
            ))}
          </div>
        ) : null,
    },
    {
      key: "publicationDate",
      content: profile.publicationDate ? (
        <p className="text-sm leading-7 text-secondary sm:text-base">{profile.publicationDate}</p>
      ) : null,
    },
    {
      key: "publicationType",
      content: profile.publicationType ? (
        <p className="text-sm leading-7 text-secondary sm:text-base">{profile.publicationType}</p>
      ) : null,
    },
    {
      key: "topics",
      content:
        profile.topics && profile.topics.length > 0 ? (
          <div className="flex flex-wrap gap-2">
            {profile.topics.map((topic) => (
              <span
                key={topic}
                className="rounded-full border border-[#00709E33] bg-white px-3 py-1.5 text-sm text-secondary"
              >
                {topic}
              </span>
            ))}
          </div>
        ) : null,
    },
    {
      key: "publicationLink",
      content: profile.publicationLink ? (
        <a
          href={profile.publicationLink}
          target="_blank"
          rel="noopener noreferrer"
          className="break-all text-sm text-primary hover:underline sm:text-base"
          dir="ltr"
        >
          {profile.publicationLink}
        </a>
      ) : null,
    },
  ];

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/45 p-4">
      <div
        dir={isRtl ? "rtl" : "ltr"}
        className="relative max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-2xl bg-white p-5 shadow-xl sm:p-8"
      >
        <button
          type="button"
          onClick={onClose}
          className="absolute top-4 left-4 inline-flex size-9 items-center justify-center rounded-full border border-[#00709E26] text-secondary hover:bg-[#f4f6f8]"
          aria-label="Close"
        >
          <X className="size-4" />
        </button>

        <h2 className="mb-8 text-center text-2xl font-bold text-secondary sm:text-3xl">
          {labels.detailsTitle ?? "Publication Details"}
        </h2>

        <div className="grid gap-8 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
          <div className="space-y-5">
            {fieldRows.map(({ key, content }) => {
              if (!content) {
                return null;
              }

              return (
                <div
                  key={key}
                  className="grid gap-2 border-b border-[#00709E14] pb-4 sm:grid-cols-[160px_minmax(0,1fr)] sm:items-start sm:gap-4"
                >
                  <p className="text-sm font-bold text-primary sm:text-base">
                    {labels[key] ?? key}
                  </p>
                  <div>{content}</div>
                </div>
              );
            })}
          </div>

          <div className="relative min-h-[320px] overflow-hidden rounded-2xl bg-[#0b2a4a] sm:min-h-[420px]">
            {profile.coverImage ? (
              <Image
                src={profile.coverImage}
                alt={title}
                fill
                className="object-cover"
                sizes="(max-width: 1024px) 100vw, 420px"
              />
            ) : null}
            <div className="absolute inset-0 bg-linear-to-t from-[#041428f2] via-[#04142866] to-transparent" />
            <div className="absolute inset-x-0 bottom-0 space-y-3 p-6 text-white">
              {profile.publicationDate ? (
                <p className="text-sm font-medium text-white/85">{profile.publicationDate}</p>
              ) : null}
              {title ? (
                <h3 className="text-xl font-bold leading-9 sm:text-2xl sm:leading-10">{title}</h3>
              ) : null}
              <div className="h-1 w-16 rounded-full bg-primary" />
            </div>
          </div>
        </div>

        <div className="mt-8 flex flex-wrap justify-center gap-3 sm:gap-4">
          {languageVersionAr ? (
            <a
              href={languageVersionAr.href ?? profile.publicationLink ?? "#"}
              target="_blank"
              rel="noopener noreferrer"
              className="min-w-[180px] rounded-xl bg-secondary px-6 py-3.5 text-center text-sm font-bold text-white transition-colors hover:bg-secondary/90 sm:text-base"
            >
              {languageVersionAr.label || labels.inArabicLabel || "In Arabic"}
            </a>
          ) : null}
          {languageVersionEn ? (
            <a
              href={languageVersionEn.href ?? profile.publicationLink ?? "#"}
              target="_blank"
              rel="noopener noreferrer"
              className="min-w-[180px] rounded-xl bg-primary px-6 py-3.5 text-center text-sm font-bold text-white transition-colors hover:bg-primary/90 sm:text-base"
            >
              {languageVersionEn.label || labels.inEnglishLabel || "In English"}
            </a>
          ) : null}
        </div>
      </div>
    </div>
  );
}

type ProjectProfile = {
  layout?: "simple" | "rich";
  heroImage?: string;
  mapImage?: string;
  valuesContent?: string;
  policyToolsContent?: string;
  sources?: { name: string; image: string }[];
  founders?: { role: string; name: string }[];
  references?: {
    projectLink?: string;
    notes?: string;
    references?: string[];
  };
  relatedProjects?: RelatedProject[];
};

function projectProfileFromItem(
  item: Record<string, unknown>,
  detail: Record<string, unknown>,
): ProjectProfile {
  const merged = { ...detail, ...item } as ProjectProfile;
  const nestedDetail = item.detail;
  if (nestedDetail && typeof nestedDetail === "object") {
    return { ...merged, ...(nestedDetail as ProjectProfile) };
  }
  return merged;
}

function ProjectProfileSection({
  profile,
  labels,
  locationLabel,
  isRtl,
}: {
  profile: ProjectProfile;
  labels: {
    projectDescriptionTitle?: string;
    projectValuesTitle?: string;
    projectPolicyToolsTitle?: string;
    viewSourceLabel?: string;
    foundersTitle?: string;
    referencesAccordionTitle?: string;
    projectLinkLabel?: string;
    notesLabel?: string;
    referencesLabel?: string;
    relatedProjectsTitle?: string;
  };
  locationLabel?: string;
  isRtl: boolean;
}) {
  const layout = profile.layout === "rich" ? "rich" : "simple";

  const descriptionBlock = (
    <section className="space-y-5">
      <h2 className="text-xl font-bold text-secondary sm:text-2xl">
        {labels.projectDescriptionTitle ?? "Project Description"}
      </h2>
      {profile.valuesContent ? (
        <div>
          <h3 className="mb-2 text-lg font-bold text-secondary">
            {labels.projectValuesTitle ?? "Values"}
          </h3>
          <p className="text-base leading-8 text-[#4d5a6f] sm:text-lg">{profile.valuesContent}</p>
        </div>
      ) : null}
      {profile.policyToolsContent ? (
        <div>
          <h3 className="mb-2 text-lg font-bold text-secondary">
            {labels.projectPolicyToolsTitle ?? "Policy Tools"}
          </h3>
          <p className="text-base leading-8 text-[#4d5a6f] sm:text-lg">
            {profile.policyToolsContent}
          </p>
        </div>
      ) : null}
    </section>
  );

  const mapBlock =
    profile.mapImage ? (
      <div className="space-y-3">
        <div className="relative aspect-video overflow-hidden rounded-xl bg-[#eef6fa]">
          <Image
            src={profile.mapImage}
            alt={locationLabel ?? ""}
            fill
            className="object-contain p-4"
            sizes="(max-width: 768px) 100vw, 768px"
          />
        </div>
        {locationLabel ? (
          <p className="text-center text-lg font-bold text-secondary">{locationLabel}</p>
        ) : null}
      </div>
    ) : null;

  if (layout === "simple") {
    return (
      <div className="space-y-8">
        {descriptionBlock}
        {mapBlock}
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {profile.heroImage || profile.mapImage ? (
        <div className="grid gap-4 lg:grid-cols-2">
          {profile.heroImage ? (
            <div className="relative aspect-video overflow-hidden rounded-xl">
              <Image
                src={profile.heroImage}
                alt=""
                fill
                className="object-cover"
                sizes="(max-width: 768px) 100vw, 480px"
              />
            </div>
          ) : null}
          {profile.mapImage ? (
            <div className="relative aspect-video overflow-hidden rounded-xl bg-[#eef6fa]">
              <Image
                src={profile.mapImage}
                alt=""
                fill
                className="object-contain p-4"
                sizes="(max-width: 768px) 100vw, 480px"
              />
            </div>
          ) : null}
        </div>
      ) : null}

      {descriptionBlock}

      {layout === "rich" && profile.sources && profile.sources.length > 0 ? (
        <div className="grid gap-4 sm:grid-cols-3">
          {profile.sources.map((source) => (
            <article
              key={source.name}
              className="overflow-hidden rounded-xl bg-[#eef6fa]"
              dir={isRtl ? "rtl" : "ltr"}
            >
              <div className="relative aspect-4/3">
                <Image
                  src={source.image}
                  alt={source.name}
                  fill
                  className="object-cover"
                  sizes="240px"
                />
              </div>
              <div className="p-4 text-center">
                <h3 className="mb-2 text-base font-bold text-secondary">{source.name}</h3>
                <span className="text-sm font-medium text-primary">
                  {labels.viewSourceLabel ?? "View Source"}
                </span>
              </div>
            </article>
          ))}
        </div>
      ) : null}

      {layout === "rich" && profile.founders && profile.founders.length > 0 ? (
        <section>
          <h2 className="mb-4 text-xl font-bold text-secondary sm:text-2xl">
            {labels.foundersTitle ?? "Founders"}
          </h2>
          <div className="grid gap-3 sm:grid-cols-2">
            {profile.founders.map((founder) => (
              <div
                key={`${founder.role}-${founder.name}`}
                className="rounded-xl border border-[#00709E14] bg-[#eef6fa] p-4"
              >
                <p className="mb-1 text-sm font-bold text-primary">{founder.role}</p>
                <p className="text-base text-secondary">{founder.name}</p>
              </div>
            ))}
          </div>
        </section>
      ) : null}

      {layout === "rich" && profile.references ? (
        <section className="rounded-xl border border-[#00709E14] bg-white p-5">
          <h2 className="mb-4 text-xl font-bold text-secondary sm:text-2xl">
            {labels.referencesAccordionTitle ?? "References"}
          </h2>
          {profile.references.projectLink ? (
            <p className="mb-3 text-sm text-[#4d5a6f]">
              <span className="font-bold text-secondary">
                {labels.projectLinkLabel ?? "Project Link"}:
              </span>{" "}
              <a
                href={profile.references.projectLink}
                className="text-primary hover:underline"
                dir="ltr"
              >
                {profile.references.projectLink}
              </a>
            </p>
          ) : null}
          {profile.references.notes ? (
            <p className="mb-3 text-sm leading-7 text-[#4d5a6f]">
              <span className="font-bold text-secondary">{labels.notesLabel ?? "Notes"}:</span>{" "}
              {profile.references.notes}
            </p>
          ) : null}
          {profile.references.references && profile.references.references.length > 0 ? (
            <div>
              <p className="mb-2 text-sm font-bold text-secondary">
                {labels.referencesLabel ?? "References"}
              </p>
              <ul
                className={cn(
                  "list-disc space-y-1 ps-5 text-sm leading-7 text-[#4d5a6f]",
                  isRtl ? "text-right" : "text-left",
                )}
              >
                {profile.references.references.map((reference) => (
                  <li key={reference}>{reference}</li>
                ))}
              </ul>
            </div>
          ) : null}
        </section>
      ) : null}

      {layout === "rich" && profile.relatedProjects && profile.relatedProjects.length > 0 ? (
        <div className="rounded-2xl bg-white p-6 shadow-[1px_1px_18.6px_0px_#111F421C] sm:p-8">
          <h2 className="mb-6 text-xl font-bold text-secondary sm:text-2xl">
            {labels.relatedProjectsTitle ?? "Related Projects"}
          </h2>
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {profile.relatedProjects.map((project) => (
              <RelatedProjectCard
                key={`${project.city}-${project.country}-${project.dateRange}`}
                project={project}
                isRtl={isRtl}
              />
            ))}
          </div>
        </div>
      ) : null}
    </div>
  );
}

export function DirectoryItemDetailPanel({
  tab,
  number,
  isRtl,
  fallbackUi,
  initialData,
  onBack,
}: Props) {
  const locale = useLocale();
  const [data, setData] = useState<DirectoryItemDetail | null>(null);
  const [loading, setLoading] = useState(true);
  const [authorName, setAuthorName] = useState("");
  const [body, setBody] = useState("");
  const [submitting, setSubmitting] = useState(false);
  const [message, setMessage] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    const response = await fetchDirectoryItem(tab, number, locale);
    if (response) {
      setData(response);
    } else if (initialData) {
      setData(initialData);
    } else {
      setData(await getFallbackDirectoryItem(locale, tab, number));
    }
    setLoading(false);
  }, [initialData, locale, number, tab]);

  useEffect(() => {
    void load();
  }, [load]);

  const ui = { ...fallbackUi, ...data?.ui };
  const item = data?.item ?? {};
  const detail = (item.detail ?? {}) as DetailContent;
  const legacyParagraphs = detail.paragraphs ?? [];
  const sections = detail.sections ?? [];
  const discussions = data?.discussions ?? [];
  const layout = detail.layout === "rich" ? "rich" : "simple";
  const isOrganization = tab === "organizations";
  const isProject = tab === "projects";
  const isPublication = tab === "publications";
  const showDiscussions = tab === "cities" && layout === "rich";
  const organizationProfile = isOrganization
    ? organizationProfileFromItem(item, detail as Record<string, unknown>)
    : null;
  const projectProfile = isProject
    ? projectProfileFromItem(item, detail as Record<string, unknown>)
    : null;
  const publicationProfile = isPublication
    ? publicationProfileFromItem(item, detail as Record<string, unknown>)
    : null;
  const title = itemTitle(item, detail);
  const country = isOrganization
    ? (organizationProfile?.country ?? "")
    : isProject || isPublication
      ? ""
      : detail.country ??
      (typeof item.name === "string"
        ? item.name.split("،").slice(1).join("،").trim() ||
          item.name.split(",").slice(1).join(",").trim()
        : "");
  const subtitle = isOrganization
    ? organizationProfile?.type ?? ""
    : isProject || isPublication
      ? ""
      : [
        typeof item.description === "string" ? item.description : "",
        detail.population ? `(${detail.population})` : "",
      ]
        .filter(Boolean)
        .join(" ");

  const onSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    setSubmitting(true);
    setMessage(null);
    setError(null);

    try {
      await submitDirectoryDiscussion(tab, number, { authorName, body }, locale);
      setAuthorName("");
      setBody("");
      setMessage(ui.commentSuccess ?? "Submitted.");
      await load();
    } catch {
      setError(ui.commentError ?? "Error.");
    } finally {
      setSubmitting(false);
    }
  };

  if (isPublication && publicationProfile) {
    if (loading) {
      return (
        <div className="bg-[#f4f6f8] py-10 sm:py-14">
          <div className="mx-auto max-w-4xl px-4 sm:px-6">
            <p className="text-secondary">...</p>
          </div>
        </div>
      );
    }

    return (
      <PublicationProfileSection
        profile={{ ...publicationProfile, name: title }}
        labels={ui.publicationFields ?? {}}
        isRtl={isRtl}
        onClose={onBack}
      />
    );
  }

  return (
    <div className="bg-[#f4f6f8] py-10 sm:py-14">
      <div className="mx-auto max-w-4xl px-4 sm:px-6">
        <button
          type="button"
          onClick={onBack}
          className="mb-6 text-sm font-medium text-primary hover:underline"
        >
          {ui.backToListLabel ?? "Back"}
        </button>

        {loading ? (
          <p className="text-secondary">...</p>
        ) : (
          <div dir={isRtl ? "rtl" : "ltr"} className="space-y-8">
            <div className="rounded-2xl bg-white p-6 shadow-[1px_1px_18.6px_0px_#111F421C] sm:p-8">
              <div className="mb-6 flex flex-wrap items-start justify-between gap-4">
                <div>
                  <h1 className="mb-2 text-3xl font-bold text-secondary sm:text-4xl">
                    {title}
                  </h1>
                  {country ? (
                    <p className="mb-2 flex items-center gap-2 text-base text-[#4d5a6f]">
                      {isOrganization ? (
                        <OrganizationCountryFlag code={organizationProfile?.countryCode} />
                      ) : null}
                      {country}
                    </p>
                  ) : null}
                  {subtitle ? (
                    <p className="text-sm text-[#4d5a6f] sm:text-base">{subtitle}</p>
                  ) : null}
                </div>

                <div className="flex gap-2">
                  <button
                    type="button"
                    className="inline-flex items-center gap-2 rounded-lg border border-[#00709E26] px-4 py-2 text-sm font-medium text-secondary hover:bg-[#f4f6f8]"
                  >
                    <Share2 className="size-4" />
                    {ui.shareLabel ?? "Share"}
                  </button>
                  <button
                    type="button"
                    className="inline-flex items-center gap-2 rounded-lg border border-[#00709E26] px-4 py-2 text-sm font-medium text-secondary hover:bg-[#f4f6f8]"
                  >
                    <Download className="size-4" />
                    {ui.downloadLabel ?? "Download"}
                  </button>
                </div>
              </div>

              {isOrganization && organizationProfile ? (
                <OrganizationProfileSection
                  profile={organizationProfile}
                  labels={ui.organizationFields ?? {}}
                  isRtl={isRtl}
                />
              ) : isProject && projectProfile ? (
                <ProjectProfileSection
                  profile={projectProfile}
                  locationLabel={title}
                  labels={{
                    projectDescriptionTitle: ui.projectDescriptionTitle,
                    projectValuesTitle: ui.projectValuesTitle,
                    projectPolicyToolsTitle: ui.projectPolicyToolsTitle,
                    viewSourceLabel: ui.viewSourceLabel,
                    foundersTitle: ui.foundersTitle,
                    referencesAccordionTitle: ui.referencesAccordionTitle,
                    projectLinkLabel: ui.projectLinkLabel,
                    notesLabel: ui.notesLabel,
                    referencesLabel: ui.referencesLabel,
                    relatedProjectsTitle: ui.relatedProjectsTitle,
                  }}
                  isRtl={isRtl}
                />
              ) : sections.length > 0 ? (
                <div className="space-y-10">
                  {sections.map((section) => (
                    <section key={section.title ?? section.paragraphs?.[0]} className="space-y-5">
                      {section.title ? (
                        <h2 className="text-xl font-bold text-secondary sm:text-2xl">
                          {section.title}
                        </h2>
                      ) : null}

                      {section.image ? (
                        <div className="relative aspect-video overflow-hidden rounded-xl">
                          <Image
                            src={section.image}
                            alt={section.title ?? title}
                            fill
                            className="object-cover"
                            sizes="(max-width: 768px) 100vw, 768px"
                          />
                        </div>
                      ) : null}

                      {section.paragraphs?.map((paragraph) => (
                        <p
                          key={paragraph}
                          className="text-base leading-8 text-[#4d5a6f] sm:text-lg sm:leading-9"
                        >
                          {paragraph}
                        </p>
                      ))}

                      {section.figures && section.figures.length > 0 ? (
                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                          {section.figures.map((figure) => (
                            <figure key={figure.caption ?? figure.image} className="space-y-3">
                              <div className="relative aspect-4/3 overflow-hidden rounded-xl">
                                <Image
                                  src={figure.image}
                                  alt={figure.caption ?? title}
                                  fill
                                  className="object-cover"
                                  sizes="(max-width: 768px) 100vw, 240px"
                                />
                              </div>
                              {figure.caption ? (
                                <figcaption className="text-sm font-medium text-secondary">
                                  {figure.caption}
                                </figcaption>
                              ) : null}
                              {figure.address ? (
                                <p className="text-xs text-[#4d5a6f]">
                                  <span className="font-bold text-secondary">
                                    {ui.addressLabel ?? "Address"}:
                                  </span>{" "}
                                  {figure.address}
                                </p>
                              ) : null}
                              {figure.source ? (
                                <p className="text-xs text-[#4d5a6f]">
                                  <span className="font-bold text-secondary">
                                    {ui.sourceLabel ?? "Source"}:
                                  </span>{" "}
                                  {figure.source}
                                </p>
                              ) : null}
                            </figure>
                          ))}
                        </div>
                      ) : null}

                      {section.bullets && section.bullets.length > 0 ? (
                        <ul
                          className={cn(
                            "list-disc space-y-2 ps-5 text-base leading-8 text-[#4d5a6f]",
                            isRtl ? "text-right" : "text-left",
                          )}
                        >
                          {section.bullets.map((bullet) => (
                            <li key={bullet}>{bullet}</li>
                          ))}
                        </ul>
                      ) : null}
                    </section>
                  ))}
                </div>
              ) : (
                <div className="space-y-4">
                  {detail.image ? (
                    <div className="relative mb-6 aspect-video overflow-hidden rounded-xl">
                      <Image
                        src={detail.image}
                        alt={title}
                        fill
                        className="object-cover"
                        sizes="(max-width: 768px) 100vw, 768px"
                      />
                    </div>
                  ) : null}
                  {legacyParagraphs.map((paragraph) => (
                    <p
                      key={paragraph}
                      className="text-base leading-8 text-[#4d5a6f] sm:text-lg sm:leading-9"
                    >
                      {paragraph}
                    </p>
                  ))}
                </div>
              )}
            </div>

            {detail.relatedProjects && detail.relatedProjects.length > 0 ? (
              <div className="rounded-2xl bg-white p-6 shadow-[1px_1px_18.6px_0px_#111F421C] sm:p-8">
                <h2 className="mb-6 text-xl font-bold text-secondary sm:text-2xl">
                  {ui.relatedProjectsTitle ?? "Related Projects"}
                </h2>
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  {detail.relatedProjects.map((project) => (
                    <RelatedProjectCard
                      key={`${project.city}-${project.country}`}
                      project={project}
                      isRtl={isRtl}
                    />
                  ))}
                </div>
              </div>
            ) : null}

            {detail.cta?.title ? (
              <div className="rounded-2xl bg-primary px-6 py-8 text-center text-white sm:px-10 sm:py-10">
                <h2 className="mb-3 text-2xl font-bold sm:text-3xl">{detail.cta.title}</h2>
                {detail.cta.description ? (
                  <p className="mx-auto mb-6 max-w-2xl text-sm leading-7 text-white/90 sm:text-base">
                    {detail.cta.description}
                  </p>
                ) : null}
                {detail.cta.href ? (
                  <a
                    href={detail.cta.href}
                    className="inline-flex rounded-lg bg-white px-6 py-3 text-sm font-bold text-primary hover:bg-white/90"
                  >
                    {detail.cta.button ?? "Contact"}
                  </a>
                ) : null}
              </div>
            ) : ui.directoryCta?.title ? (
              <div className="rounded-2xl bg-primary px-6 py-8 text-center text-white sm:px-10 sm:py-10">
                <h2 className="mb-3 text-2xl font-bold sm:text-3xl">{ui.directoryCta.title}</h2>
                {ui.directoryCta.description ? (
                  <p className="mx-auto mb-6 max-w-2xl text-sm leading-7 text-white/90 sm:text-base">
                    {ui.directoryCta.description}
                  </p>
                ) : null}
                {ui.directoryCta.href ? (
                  <a
                    href={ui.directoryCta.href}
                    className="inline-flex rounded-lg bg-white px-6 py-3 text-sm font-bold text-primary hover:bg-white/90"
                  >
                    {ui.directoryCta.button ?? "Contact"}
                  </a>
                ) : null}
              </div>
            ) : null}

            {showDiscussions ? (
            <div className="rounded-2xl bg-white p-6 shadow-[1px_1px_18.6px_0px_#111F421C] sm:p-8">
              <h3 className="mb-6 text-xl font-bold text-secondary sm:text-2xl">
                {ui.discussionTitle ?? "Discussion"}
              </h3>

              <div className="mb-8 space-y-4">
                {discussions.length === 0 ? (
                  <p className="text-sm text-[#4d5a6f]">
                    {ui.addCommentLabel ?? "Add a comment"}
                  </p>
                ) : (
                  discussions.map((discussion) => (
                    <article
                      key={discussion.id}
                      className="rounded-xl border border-[#00709E14] bg-[#eef6fa] p-4"
                    >
                      <p className="mb-2 text-sm font-bold text-secondary">
                        {discussion.author}
                      </p>
                      <p className="text-sm leading-7 text-[#4d5a6f] sm:text-base">
                        {discussion.body}
                      </p>
                    </article>
                  ))
                )}
              </div>

              <form onSubmit={onSubmit} className="space-y-4">
                <h4 className="text-base font-bold text-secondary">
                  {ui.addCommentLabel ?? "Add a comment"}
                </h4>

                <label className="block">
                  <span className="mb-2 block text-sm font-medium text-secondary">
                    {ui.authorNameLabel ?? "Name"}
                  </span>
                  <input
                    type="text"
                    required
                    value={authorName}
                    onChange={(e) => setAuthorName(e.target.value)}
                    className="h-11 w-full rounded-lg border border-[#00709E26] bg-white px-4 text-sm text-secondary outline-none focus:border-primary"
                  />
                </label>

                <label className="block">
                  <span className="mb-2 block text-sm font-medium text-secondary">
                    {ui.commentBodyLabel ?? "Comment"}
                  </span>
                  <textarea
                    required
                    rows={4}
                    value={body}
                    onChange={(e) => setBody(e.target.value)}
                    className="w-full rounded-lg border border-[#00709E26] bg-white px-4 py-3 text-sm text-secondary outline-none focus:border-primary"
                  />
                </label>

                {message ? (
                  <p className="text-sm text-green-700">{message}</p>
                ) : null}
                {error ? <p className="text-sm text-red-600">{error}</p> : null}

                <button
                  type="submit"
                  disabled={submitting}
                  className="rounded-lg bg-primary px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-primary/90 disabled:opacity-60"
                >
                  {ui.submitCommentLabel ?? "Submit"}
                </button>
              </form>
            </div>
            ) : null}
          </div>
        )}
      </div>
    </div>
  );
}
