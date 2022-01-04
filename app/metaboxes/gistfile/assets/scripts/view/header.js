import React, { useState, useContext } from 'react';
//import { Context } from "../components/FileCount";

import { Context } from "../components/Provider";

export default function Header() {
	const { name } = useContext(Context);
	//https://codesandbox.io/s/react-context-api-example-0ghhy?file=/src/Provider.js:0-475
	//https://stackoverflow.com/questions/61836485/passing-data-to-sibling-components-with-react-hooks
	//const { fileCount } = useContext(Context);
	const fileCount = 5;

	return (
		<>
		</>
	);
  }