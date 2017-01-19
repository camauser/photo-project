<?php
/* 
 * Cam Auser
 * November 9, 2016
 */

/*****************************************************
 * This file is an extension of Form, and will provide
 * the exact same functionality, except optimized for websites
 * using Bootstrap.
 * 
 */


include_once("Form.php");

class BootstrapForm extends Form
{
   
    
    /**************************************************
        Function Constructor
     *  Purpose  Basic constructor function for this class
     *           This will make use of our parent class SBody
     *           but set up a table to surrond all the information 
     * Params    sName = Name of the form
     *           sAction where the form is going to be submitted
     *           sMethod How the form is going to be submitted
     *           sOptions = Any addition formatting (ie. CSS) options for the
     *           table
     * *************************************************/
    public function __construct($sName, $sAction, $sMethod = "POST") 
    {
        parent::__construct($sName, $sAction, $sMethod);
        //$this->sBody .= "<table $sOptions>\n";
        
        
        
    }
    
    public function dumpForm($sSubmitText = "Submit Form", $sResetText = "Clear Form") 
    {
       //$this->sBody .= "<tr><td><input type='submit' value='$sSubmitText'/> </td>".
         //               "<td> <input type='reset' value='$sResetText'/></td></tr></table>";
        $this->sBody .= "<div class='form-group'><div class='col-sm-1'><input type='submit' value='$sSubmitText' class='btn btn-success'></div>"
                . "<div class='col-sm-1'><input type='reset' value='$sResetText' class='btn btn-warning'></div></div></form>";
       return $this->sBody;                   
    }
    
    /*********************************************************************
     * Function addText  - This will add a new text entry on a new row in the
     * form. 
     * Parameters
     *      Label - Label which is to be associated with the text box
     *      Name - Name of the text box.
     *      Options = Any addition options such as size or css class name tht
     *                might be associated with this text box.
     ***************************************************************/
    
    // Done
    public function addText($sLabel, $sName, $sOptions="")
    {
        //$this->sBody.="<tr><td>$sLabel</td><td><input type='text' name='$sName' " .
                        //"id='$sName' $sOptions/></td></tr>\n";
        $this->sBody .= "<div class='form-group'>\n\t<label for='$sName'>$sLabel</label>\n\t<input type='text' class='form-control' name='$sName' id='$sName' $sOptions>\n</div>\n";
        
    }
    
    
     // Done
    public function addPassword($sLabel, $sName, $sOptions="")
    {
        //$this->sBody.="<tr><td>$sLabel</td><td><input type='password' name='$sName' " .
                        //"id='$sName' $sOptions/></td></tr>\n";
        $this->sBody .= "<div class='form-group'>\n\t<label for='$sName'>$sLabel</label>\n\t<input type='password' class='form-control' name='$sName' id='$sName' $sOptions>\n</div>\n";
        
    }
    
    // Done
    public function addSelText($sLabel, $sSelectString) {
        
        //$this->sBody .= "<tr><td> $sLabel</td><td> $sSelectString </td></tr>\n";
        $this->sBody .= "<div class='form-group'><label>$sLabel</label>$sSelectString</div>";
        
    }
    
    
    // Done
    public function addRad($sName, $sLabel, $sValue, $sOptions="") {
        
        //$this->sBody .= "<tr><td>$sLabel</td><td ><input type='radio' name='$sName' ".
          //              "id='$sName' value='$sValue' $sOptions/> </td></tr>\n";
        $this->sBody .= "<div class='radio'><label><input type='radio' name='$sName' id='$sName' value='$sValue' $sOptions>$sLabel</label></div>";
       
    }
    
    
    // Done
    public function addCheck($sLabel, $sName, $sOptions="")
    {
        //$this->sBody.="<tr><td>$sLabel</td><td><input type='checkbox' name='$sName' " .
                        //"id='$sName' $sOptions/></td></tr>\n";
        $this->sBody .= "<div class='checkbox'>\n<label><input type='checkbox' name='$sName' id='$sName' $sOptions>$sLabel</label>\n</div>";
        
    }
    
    
    public function addHeader($sHeader)
    {
        //$this->sBody .= "<tr><td colspan='2' style='text-align:center'> $sHeader </td></tr>\n";
        $this->sBody .= "<div class='form-group'><strong>$sHeader</strong></div>";
        
    }
    
    public function addHidden($sName, $sValue)
    {
        $this->sBody .= "<input type='hidden' name='$sName' value='$sValue' >";
    }
    
    public function addDate($sLabel, $sName, $sOptions="")
    {
        $this->sBody .= "<div class='form-group'>\n\t<label for='$sName'>$sLabel</label>\n\t<input type='date' class='form-control' name='$sName' id='$sName' $sOptions>\n</div>\n";
    }
    
    public function addNumber($sLabel, $sName, $sOptions="", $sGlyph="")
    {
        $this->sBody .= "<div class='form-group'>\n\t<label for='$sName'>$sLabel</label>\n\t";
        $this->sBody .= "<input type='number' class='form-control' name='$sName' id='$sName' $sOptions>\n";
        if ($sGlyph != "")
        {
            $this->sBody .= "<span class='form-group-addon inner-addon left-addon'><span class='glyphicon'>$sGlyph</span>"
                . "</span>";
        }
        
        $this->sBody .= "</div>\n";
    }
    
    public function addSelection($sLabel, $sName, $sSelList, $sOptions="")
    {
        $this->sBody .= "<div class='form-group'>\n\t<label for='$sName'>$sLabel</label>\n\t";
        $this->sBody .= "<select name='$sName' id='$sName' class='form-control' $sOptions>";
        foreach ($sSelList as $key=>$value)
        {
            $this->sBody .= "<option value='$key'> $value </option>";
        }
        $this->sBody .= "</select></div>\n";

        
        
    }
}