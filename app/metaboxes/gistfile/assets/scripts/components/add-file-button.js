import React, { useState, useContext } from "react";
import Button from "@mui/material/Button";
import AddCircleOutlineIcon from "@mui/icons-material/AddCircleOutline";

import { Context } from "../components/Provider";

export default function AddFileButton() {
	const { setFileEditorState, setEditorDataFilename, setPrevEditorDataFilename } = useContext(Context);

	const handleClickOpen = () => {
		setEditorDataFilename("");
		setPrevEditorDataFilename("");
		setFileEditorState(true);
	};

	return (
		<div>
			<Button
				variant="outlined"
				onClick={handleClickOpen}
				startIcon={<AddCircleOutlineIcon />}
			>
				{gist_sync_files_metabox.labels.add_file_btn_title}
			</Button>
		</div>
	);
}
