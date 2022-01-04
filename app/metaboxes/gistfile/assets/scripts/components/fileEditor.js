import React, { useState, useContext } from "react";
import Button from "@mui/material/Button";
import TextField from "@mui/material/TextField";
import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import DialogTitle from "@mui/material/DialogTitle";
import Alert from "@mui/material/Alert";

import BlockIcon from '@mui/icons-material/Block';
import AddCircleOutlineIcon from '@mui/icons-material/AddCircleOutline';

import { Context } from "../components/Provider";

import Editor, { DiffEditor, useMonaco, loader } from "@monaco-editor/react";

export default function FileEditor() {
	const [fileName, setFileName] = React.useState("");
	const [fileContent, setFileContent] = React.useState("");

	const [fileNotificationContent, setNotificationContent] = React.useState();

	const {
		editorOpen,
		setFileEditorState,
		files,
		addFile,
		updateFile,
		setEditorCurrentFileContent,
		editorDataFilename,
		setEditorDataFilename,
		editorDataFileContent,
		setEditorDataFileContent,
		prevEditorDataFilename,
	} = useContext(Context);

	const handleEditorContent = (editorContent, e) => {
		//setEditorCurrentFileContent(editorContent);
		setEditorDataFileContent(editorContent);
	};

	const handleClose = () => {
		setFileName("");
		setEditorDataFileContent("");
		setNotificationContent("");
		setFileEditorState(false);
	};

	let addNewFile = () => {
		if (!editorDataFilename) {
			setNotificationContent("You need to fill in a filename");
			return;
		} else if (files.some((el) => el.fileName === editorDataFilename)) {
			setNotificationContent("That file name exists already");
			return;
		} else {
			setNotificationContent("");
		}

		let newFile = {
			fileName: editorDataFilename,
			fileContent: editorDataFileContent,
		};

		addFile(newFile);
		handleClose();
	};

	const saveFile = () => {
		if (!editorDataFilename) {
			setNotificationContent("You need to fill in a filename");
			return;
		} else if (
			editorDataFilename !== prevEditorDataFilename &&
			files.some((el) => el.fileName === editorDataFilename)
		) {
			setNotificationContent("That file name exists already");
			return;
		} else {
			setNotificationContent("");
		}

		let newFile = {
			fileName: editorDataFilename,
			fileContent: editorDataFileContent,
		};

		let fileIndex = files.findIndex(
			(el) => el.fileName == prevEditorDataFilename
		);

		updateFile(fileIndex, newFile);
		handleClose();
	};

	let FileNotification = () => (
		<Alert severity="warning">{fileNotificationContent}</Alert>
	);

	return (
		<div>
			<Dialog
				fullWidth={true}
				maxWidth="md"
				open={editorOpen}
				onClose={handleClose}
			>
				<DialogTitle>File</DialogTitle>
				<DialogContent>
					<DialogContentText>
						Add or edit a file for your Gist.
					</DialogContentText>

					{fileNotificationContent ? <FileNotification /> : null}

					<TextField
						autoFocus
						margin="dense"
						id="name"
						label="File name"
						fullWidth
						variant="standard"
						value={editorDataFilename}
						onChange={(e) => setEditorDataFilename(e.target.value)}
					/>

					<Editor
						height="90vh"
						theme="vs-dark"
						defaultLanguage="javascript"
						defaultValue="// add code"
						value={editorDataFileContent}
						onChange={handleEditorContent}
					/>
				</DialogContent>
				<DialogActions>
					<Button variant="outlined" startIcon={<BlockIcon />} size="small" onClick={handleClose}>
						{gist_sync_files_metabox.labels.cancel_btn_title}
					</Button>

					{prevEditorDataFilename ? (
						<Button variant="contained" startIcon={<AddCircleOutlineIcon />} size="small" onClick={saveFile}>Save</Button>
					) : (
						<Button variant="contained" startIcon={<AddCircleOutlineIcon />} size="small" onClick={addNewFile}>
							{gist_sync_files_metabox.labels.add_btn_title}
						</Button>
					)}
				</DialogActions>
			</Dialog>
		</div>
	);
}
