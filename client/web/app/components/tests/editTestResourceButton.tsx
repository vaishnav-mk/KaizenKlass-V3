import React, { useState } from "react";
import { Dialog, DialogContent, DialogTrigger } from "~/components/ui/dialog";
import { Label } from "../ui/label";
import { Input } from "../ui/input";
import { Textarea } from "../ui/textarea";
import axios from "axios";
import { useToast } from "../ui/use-toast";

export const EditTestResourceButton = ({
  baseUrl,
  originalDescription,
  testResourceUuid,
  handleEditTestResource,
}: {
  baseUrl: string;
  originalDescription: string;
  testResourceUuid: string;
  handleEditTestResource: (updatedTestResource: TestResource) => void;
}) => {
  const { toast } = useToast();
  const [description, setDescription] = useState<string>(originalDescription);
  const [content, setContent] = useState<string>();
  const [open, setOpen] = useState<boolean>(false);

  const editTestResource = async () => {
    try {
      const resp = await axios.put(
        `${baseUrl}/api/v2/update/test-resource/${testResourceUuid}`,
        {
          content,
          description,
        }
      );
      console.log(resp);
      toast({
        title: "Test Resource Updated!",
        description: "Test Resource has been updated",
      });
      // location.reload();
      handleEditTestResource(resp.data.test_resource);
      setOpen(false);
    } catch (error: any) {
      console.log(error.response);

      if (error.response && error.response.status === 422) {
        const errors = error.response.data.errors;

        if (errors) {
          let errorMessages = "";

          for (const [key, value] of Object.entries(errors)) {
            // Iterate through each error message for a specific key
            if (Array.isArray(value)) {
              errorMessages += `${key}: ${value.join(", ")}\n`;
            }
          }
          toast({
            title: "Invalid Fields Inputs",
            description: errorMessages.trim(),
            variant: "destructive",
          });
        } else {
          console.error("Unexpected error:", error);
        }
      } else {
        console.error("Unexpected error:", error);
      }
    }
  };
  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger className="">
        <img src="/assets/pencil.png" className="md:w-7 w-5 mb-2" alt="" />
      </DialogTrigger>
      <DialogContent>
        <div className="">
          <Label>Description</Label>
          <Textarea
            placeholder="description/answer"
            required
            value={description}
            onChange={(e) => setDescription(e.target.value)}
          />
          <Label>Content</Label>
          <Input
            placeholder="Drive link of Solution pdf (a share link that is public)"
            onChange={(e) => setContent(e.target.value)}
            required
          />
        </div>
        <div
          onClick={editTestResource}
          className="hover:text-dashboard text-xs md:text-base text-highlightSecondary border border-highlightSecondary duration-150 cursor-pointer hover:bg-highlightSecondary w-[15%] justify-center items-center flex p-1 font-base"
        >
          Submit
        </div>
      </DialogContent>
    </Dialog>
  );
};
