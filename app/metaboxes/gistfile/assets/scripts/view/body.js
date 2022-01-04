import React from "react";
//import Box from '@material-ui/core/Box';
import Fileslist from "../components/filesList.js";
import AddFileButton from "../components/add-file-button.js";
import FileEditor from "../components/fileEditor.js";

const Body = () => {
	const out = (
		<>
			<Fileslist></Fileslist>
			<AddFileButton></AddFileButton>
			<FileEditor></FileEditor>
		</>
	);

	return <>{out}</>;
};

export default Body;
