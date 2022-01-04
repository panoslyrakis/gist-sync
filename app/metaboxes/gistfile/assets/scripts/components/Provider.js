import React, { useState } from "react";

export const Context = React.createContext();

const Provider = (props) => {
	const setFileEditorState = (state) => {
		setEditorState(state);
	};

	const setEditorFiles = (files) => {
		setFiles(files);
	};

	const [name, setName] = useState("Default state");
	const [files, setFiles] = useState([]);
	const [editorOpen, setEditorState] = useState(false);
	const [editorDataFilename, setEditorDataFilename] = useState("");
	const [editorDataFileContent, setEditorDataFileContent] = useState("");
	// We need `prevEditorDataFilename` to check if we are creating a new file or editing an previously created one.
	const [prevEditorDataFilename, setPrevEditorDataFilename] = useState("");

	const addFile = (newFile) => {
		let filesList = files;

		filesList.push(newFile);
		setFiles(filesList);
	};

	const updateFile = (index, file) => {
		let filesList = files;

		filesList[index] = file;
		setFiles(filesList);
	};

	return (
		<Context.Provider
			value={{
				name,
				updateName: (name) => setName(name),
				editorOpen,
				setFileEditorState,
				files,
				setEditorFiles,
				addFile,
				updateFile,
				editorDataFilename,
				setEditorDataFilename,
				editorDataFileContent,
				setEditorDataFileContent,
				prevEditorDataFilename,
				setPrevEditorDataFilename,
			}}
		>
			{props.children}
		</Context.Provider>
	);
};

export default Provider;
