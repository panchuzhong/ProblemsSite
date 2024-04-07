<?php
include 'db.php';

// Error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

function uuidv4()
{
	return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	               mt_rand(0, 0xffff), mt_rand(0, 0xffff),
	               mt_rand(0, 0xffff),
	               mt_rand(0, 0x0fff) | 0x4000,
	               mt_rand(0, 0x3fff) | 0x8000,
	               mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	              );
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$title = $conn->real_escape_string($_POST['title']);
	$description = $conn->real_escape_string($_POST['description']);

	// Insert the problem into the Problems table
	$stmt = $conn->prepare("INSERT INTO Problems (Title, Description) VALUES (?, ?)");
	$stmt->bind_param("ss", $title, $description);

	if (!$stmt->execute())
	{
		echo "Error adding problem: " . $conn->error;
		$stmt->close();
		exit;
	}

	// Retrieve the ID of the inserted problem
	$problemId = $stmt->insert_id;
	$stmt->close();

	if (isset($_FILES["fileToUpload"]))
	{
		// Loop through each file
		for ($i = 0; $i < count($_FILES["fileToUpload"]["name"]); $i++)
		{
			// Check for upload error
			if ($_FILES["fileToUpload"]["error"][$i] == UPLOAD_ERR_OK)
			{
				// Check if file size is within the limit
				if ($_FILES["fileToUpload"]["size"][$i] > 5000000)   // Example size limit: 5MB
				{
					echo "Sorry, your " . $i . "th file is too large.";
					continue; // Skip this file and continue with the next one
				}

				$originalName = $_FILES["fileToUpload"]["name"][$i];
				$fileType = pathinfo($originalName, PATHINFO_EXTENSION);
				$uuidFileName = uuidv4() . '.' . $fileType;
				$targetDir = "files/" . $fileType . "/";

				// Create target directory if it doesn't exist
				if (!file_exists($targetDir))
				{
					mkdir($targetDir, 0777, true);
				}

				$targetFilePath = $targetDir . $uuidFileName;

				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $targetFilePath))
				{
					// Insert file information into the Graphs table
					$stmt = $conn->prepare("INSERT INTO Graphs (ProblemID, FileName, GraphType) VALUES (?, ?, ?)");
					$stmt->bind_param("iss", $problemId, $uuidFileName, $fileType);
					$stmt->execute();
					$stmt->close();
				}
				else
				{
					// Handle file upload error
					echo "There was an error uploading your " . $i . "th file.";
					continue; // Skip this file and continue with the next one
				}
			}
			elseif ($_FILES["fileToUpload"]["error"][$i] !== UPLOAD_ERR_NO_FILE)
			{
				// Handle file upload errors except for the 'no file uploaded' case
				echo "Error uploading file: " . $_FILES["fileToUpload"]["error"][$i];
				continue; // Skip this file and continue with the next one
			}
		}
	}

	$conn->close();
	// Redirect to the list of problems or display success message
	echo "<script>alert('Problem and associated images added successfully.'); window.location = 'list_problems.php';</script>";
}
else
{
	echo "Invalid request method.";
}
?>
