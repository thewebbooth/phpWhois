<?php
/*
              NM
              MO
       +     MM
       M     MM
      MM    $MZ
      M     MM
     MM     M8        MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
    MM     MM         MM                                           MM
MMNZMMDMMMZMM   ZM+   MM  +M7                                      MM
   MM7,   OM~ MM7?MM  MMZMM~ MM                                    MM
   MM     MM,MM  ~MM  MMM8    M                                    MM
  MM   ,  MMMM   MMM  MM+   +M                             $~      MM
  MM    ?ZMMM    MM= MMM  MMM    O        $                M       MM
 MMI   D~MMM    =MM  MMMMM+     M         M                M       MM
 MM    M MMD    MM+ +MMM      NM          M                M       MM
 MM$  M  MM     MM   MMM    MMM           M               MM       MM
 ,MMMM  +M:     MM    MMMMMN?M+    M=    MM               MM       MM
                  ,   MM    MM    $M     MM     MMMM      MM MMMMM MM                                             M
                      MM   MM     M$    $M?   MMM   MD   8MMI    MMMM                                            MM
                      MM   MD    OM     MM   MM7   ,M    MM      MMMM                                     ,      MM
                      MM  MM     M,     MM  MM    MM     MN     DMMMM                                     M     MM
                      MM  MM    MM     MM, MMN,8MMZ     NM      MMMMM                                     M     MM
                      MM ~M    8MZ     MM  MM         8 MM     MMMMMM                                    MM    +MD
                      MM ,M    MM     MM   M:       MD 8M,   MMM MZMM                                   +M     MM
                      MM  MZ  M M    $M?   MM    OMM   7M  IMM8 ~M MM                                   MD     MN
                      MM   ZMO  ,    MM     ~MMMM,     NDMMM,   MM MMMMD       :NM8          DMN,  MM87MM78MM8MM    DO
                      MM         ZMMM                           MMMMM  OM    ,MMM  ZM      MMM: ,MM   ~MM     MM =MM MM
                      MM                                        MM,MM  7M   NMM     $M    MM+     M7  MM     +MZMM   MMO
                      MM                                       MM  MM  MM  NMN       MZ  MM       MM  M8     MMMM    MM
                      MM                                       MM  MM 8MM  MM       $M= MM        MM MM    M MMM,   DMM
                      MM                                      +M   MM8MM  MM        MM  M~       MM, MM    M:MMN    MM
                      MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNMMM   MZ       MM  ~M       MMD  MM   M$MMM     MM
                                                              M$  IMMO    ZM     MMM    M     NMM   ,MM  D8 MM?    +MD
                                                                NMM7       ZMMMMMM       MMMMMM~     MMMM?  MM      MZ I
                                                                                                                     8MM


------ Copyright (c) 2012 The Web Booth, Sixpenny Handley, Dorset, UK.  ------------------------------------------------
------ https://thewebbooth.co.uk/home / mailto:chris@thewebbooth.co.uk / 0172 555 2430 ------------------------------- */



class WhoisParser
{



private $TextLines;
private $ItemDefs;
private $FullTillEnd;
private $StructuredOutput;



public function __construct( $TextLines, $ItemDefs, $FullTillEnd=null )
{
	$this->TextLines = $TextLines;
	$this->ItemDefs = $ItemDefs;
	$this->FullTillEnd = $FullTillEnd;
	$this->StructuredOutput = array( );
}



public function Parse( )
{
	$NumLines = count( $this->TextLines );
	$NumItemDefs = count( $this->ItemDefs );
	$DefKeys = array_keys( $this->ItemDefs );
	$x = 0;
	do {
		$L = trim( $this->TextLines[ $x ] );

		for( $y = 0; $y < $NumItemDefs; $y++ )
		{
			$K = $DefKeys[ $y ];
			$V = $this->ItemDefs[ $K ];
			
			
			
			if( !empty( $this->FullTillEnd ) && $V == $this->FullTillEnd && $L == $V ) // Full till end
			{
				$NextLine = '';
				$LineValues = array( );
				++$x;
				do {
					$NextLine = $this->TextLines[ $x ];
					$LineValues[] = $NextLine;
					++$x;
				} while( $x < $NumLines );
				$this->assign( $K, $LineValues );
				break;
			}
			else if( $L == $V ) // Full match need following rows until blank
			{
				$NextLine = '';
				$LineValues = array( );
				do {
					++$x;
					$NextLine = trim( $this->TextLines[ $x ] );
					if( $NextLine != '' )
						$LineValues[] = $NextLine;
				} while( $NextLine != '' );
				$this->assign( $K, $LineValues );
				break;
			}
			else if( substr( $L, 0, strlen( $V ) ) == $V ) // Part match, get following value
			{
				$Value = trim( substr( $L, strlen( $V ) ) );
				$this->assign( $K, $Value );
				break;
			}
		}
		++$x;
	} while( $x < $NumLines );
	
	return $this->StructuredOutput;
}



private function assign_recursive( $array, $parts, $value )
{
	$key = array_shift( $parts );

	if( count( $parts ) == 0 )
	{
		if( !$key )
			$array[] = $value;
		else
			$array[ $key ] = $value;
	}
	else
	{
		if( !isset( $array[ $key ] ) )
			$array[ $key ] = [];
		$array[ $key ] = assign_recursive( $array[ $key ], $parts, $value );
	}
	return $array;
}



private function assign( $vdef, $value )
{
	$this->StructuredOutput = assign_recursive( $this->StructuredOutput, explode( '.', $vdef ), $value );
}



} // End of class