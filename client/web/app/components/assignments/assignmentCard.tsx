import { Link } from "@remix-run/react";
import React from "react";

export const AssignmentCard = ({
  title,
  assignment_uuid,
  subject,
  subject_uuid,
}: {
  title: string;
  assignment_uuid: string;
  subject?: string;
  subject_uuid?: string;
}) => {
  const isMobileViewport = window.innerWidth < 768;
  const truncatedTitle = title.length > 10 ? title.slice(0, 10) + "..." : title;

  return (
    <Link
      to={`/assignments/${assignment_uuid}`}
      className="bg-mainLighter h-max-36 md:h-32 w-full flex rounded-2xl items-center justify-between hover:border-highlight border border-mainLighter duration-200 transition-all space-y-3 p-5"
    >
      <div className="w-full">
        <Link
          to={`/assignments/${assignment_uuid}`}
          className="flex justify-between space-x-5 w-full items-center"
        >
          <h2 className="text-4xl font-base text-highlight">
            {!isMobileViewport ? title : truncatedTitle}
          </h2>
          <img src="/assets/assignment.svg" className="w-12" alt="" />
        </Link>
        {subject && subject_uuid && (
          <Link
            to={`/subjects/assignments/${subject_uuid}`}
            className="text-highlightSecondary font-base"
          >
            <div className="flex space-x-1">
              <p>{subject}</p>
              <img src="/assets/book.svg" alt="" />
            </div>
          </Link>
        )}
      </div>
    </Link>
  );
};
