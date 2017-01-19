<?php

/*
 * Cam Auser
 * November 9, 2016
 */

class Form
{
    //Attribute section - for this form class we are going to have single
    // attribute that contains a string that will represent our form.
    
    protected $sBody;
    
    
    /*********************************************
     * Function Constructor - All forms will start with the tag <form...
     * Our constructor will take 2 - 3 paramaters that will specify the Name/ID,
     * Action and Method attributes for the Form.  If Method is not specified - it
     * is assumed to be an automatic variable with a value of "POST".
     * Params = Name The name of the form
     *          Action = Where the form is to be submitted
     *          Method = Type of submission
     * 
     **********************************************************************/
    
    public function __construct($sName,$sAction, $sMethod="POST")
    {
        $this->sBody = "<form name='$sName' id='$sName' action='$sAction' " .
                "Method = '$sMethod' >\n";
        
        
    }
    
    
    
    /*******************************************************************
     * Method dumpForm
     * Purpose  THis routine finished off our form creation and returns 
     * a string that is the entrie form.
     * Paramaters = SubmitText = The text that is to appear on the submit button
     *              ResetText - The text that is to appear on the clear button
     * 
     ***********************************************************/
    
    public function dumpForm($sSubmitText="Submit Form", $sResetText="Clear Form")
    {
        //Concatonate on to the end of the dump form the submit and reset buttons
        
        $this->sBody .= "<br/><input type='submit' value='$sSubmitText'/>" .
                            "<input type='reset' value='$sResetText' /> </form>\n";
        
        return $this->sBody;
        
    }
    
    
    /*********************************************************************
     * Function addText  - This will add a new text entry on a line in the
     * form. 
     * Parameters
     *      Label - Label which is to be associated with the text box
     *      Name - Name of the text box.
     *      Options = Any addition options such as size or css class name tht
     *                might be associated with this text box.
     ***************************************************************/
    public function addText($sLabel,$sName, $sOptions = "")
    {
        
        $this->sBody .= "<br/> $sLabel <input type='text' name='$sName' id='$sName' " .
                    $sOptions . "/>\n<br/>";
      
        
    }
    
    /*************************************************************
     * Function addPassword - This will add a new password field on a line in the 
     * form.
     * Parameters   sLabel - Label associated tithe the Password Field
     *              sName - THis is the name of the password field
     *              sOptions - Any additional options such as size that we may
     *                         wish to have associated with this password field
     * 
     * 
     ************************************************************/
    public function addPassword($sLabel, $sName, $sOptions="")
    {
        $this->sBody .= "<br/> $sLabel <input type='password' name='$sName' " .
                            "id='$sName'  $sOptions/>\n";
    }
    
    public function addCheck($sLabel, $sName, $sOptions="")
    {
        $this->sBody .= "<br/> $sLabel <input type='checkbox' name='$sName' " .
                            "id='$sName'  $sOptions/>\n";
    }
    
    
    
    
    /***********************************************************
     * Function addRad - This will add a new radio button to the form on a new line.
     * Params   sName  Name of the Radio Group
     *          Label   THe label to be associated with this radio button
     *          sValue  - The value which is to be associaed with this radio button
     * 
     *****************************************************************/
    public function addRad($sName, $sLabel, $sValue, $sOption="")
    {
        $this->sBody .= "<br/> $sLabel <input type='radio' value='$sValue'" .
                            "name='$sName' id='$sName' $sOption /><br/>\n";
        

                
    }
    
    
    /*********************************************************
     * Function addRad Group
     * Purpose  This will add a series of Radio butttons to the form
     * Params   sName = Name of the radio Group
     *          aEntries - An array of entries each corresponding to a radio button
     *                      each array will be an associative array that stores values
     *                      for Label and Value
     * 
     **************************************************************/
    public function addRadGroup($sName, $aEntries )
    {
        $bFirst = TRUE;
        
        foreach ($aEntries as $aEntry)
        {
            if ($bFirst)
            {
                $this->addRad($sName, $aEntry["Label"], $aEntry["Value"], "Checked='Checked'");
                $bFirst = FALSE;
            }
            else
            {
                 $this->addRad($sName, $aEntry["Label"], $aEntry["Value"] );
            }
            
        }
        
    }
    
    
    /******************************************
     * Function addHeader
     * Purpose  This routine will add a simple header to our output.
     * Params   Just the Header Label
     ***********************************************************/
    public function addHeader($sHeader)
    {
        $this->sBody .= "<h2> $sHeader </h2>";
    }
    
    /***************************************************
     * Function addTxtGroup
     * Pupose   Helper routine that will allow us to actually add a series
     * of text boxes to the form.
     * Params   aTextList = An array of Label/Name arrays that indicate the particulars
     * for our text boxes.
     *          sOther - Any additional parameters that are approriate for teh text
     *      boxes.
     * 
     */
    public function addTxtGroup($aTextList, $sOther="")
    {
        foreach ($aTextList as $aEntry)
        {
            $this->addText($aEntry["Label"], $aEntry["Name"], $sOther);
            
        }
    }
    
    /*****************************************************
     * add Selection
     * This routine will attempt to add a selection box to our form
     * Params:  $sLabel - Label associated with the selection
     *          sName   Name of the selection box
     *          sSelList    List of available options in a Name/Value pair
     *          sParams - Any Additional params to be passed to the
     *                    selection box.
     ****************************************************/
    public function addSelection($sLabel, $sName, $sSelList, $sOptions="")
    {
     
        $sSelString = "<select name='$sName' id='$sLabel' $sOptions>";
        foreach ($sSelList as $key=>$value)
        {
            $sSelString .= "<option value='$key'> $value </option>";
        }
        $sSelString .= "</select>\n";
        
        $this->addSelText($sLabel, $sSelString);
        
        
    }
     
    public function addSelText($sLabel, $sSelectString)
    {
        $this->sBody .= "<br/> $sLabel $sSelectString \n";
        
    }
    
	 /***********************************************************
     * Public Function addTextFieldsFromFile
     * Purpose  This function will read in a csv list of label/field Names from
     * a text file.   It will then add this fields to the from.
     * 
     ***********************************************************/
    public function addTextFieldsFromFile($sName)
    {
        $fp = fopen($sName,"r");
        
        if ($fp == null)
        {
            echo "Cannot open source file $sName";
            exit(1);
        }
        
        $aEntry = fgetcsv($fp);
        
        while (!feof($fp))
        {
            $this->addText($aEntry[0], $aEntry[1]);
            $aEntry = fgetcsv($fp);
            
        }
        
        fclose($fp);
        
    }
	
    
    
    
    
}




