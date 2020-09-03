<?php

header('charset=utf-8');

class Column
{

    private $title ;
    private $lineNum ;
    private $parentCSV ;     

    public function __construct( $t = "N/A" , $line , $p )
    {
        $this->title = $t ; 
        $this->lineNum = $line; 
        $this->parentCSV = $p ;
    } 

    public function getLineNumber()
    {
        return $this->lineNum ;
    }

    public function setTitle( $newText )
    {
        $this->title = $newText ; 
        return true ;
    }

    public function getTitle(  )
    {
        return $this->title ; 
    }

    function __destruct()
    {
        $this->parentCSV->deleteColumnFromTable($this);    
    }

}

class Item 
{

    private $content ;
    private $parent_column ;
    private $parentCSV ; 
    public $row ; 

    public function __construct( $t = "N/A" , $p_c , $p , $r )
    {
        $this->content = $t ; 
        $this->parent_column = $p_c ; 
        $this->parentCSV = $p ; 
        $this->row = $r ;
    } 

    public function getColumn()
    {
        return $this->parent_column ; 
    }

    public function setContent( $newContent )
    {
        $this->content = $newContent ; 
        return true ; 
    }
    
    // will give infinte loop
    // public function __set($name, $value)
    // {
    //     if (property_exists($this, $name) && $name == "row") {
    //         $this->setRow($value);
    //         return true;
    //     }
    // }

    public function getContent(  )
    {
        return $this->content ; 
    }

    public function setRow($ro)
    {
        return $this->parentCSV->setItemRow($this,$ro);
    }

    function __destruct()
    {
        $this->parentCSV->deleteItemFromTable($this);    
    }

}

class CSV
{

    private $name;
    private $columns = [] ;
    private $items = [] ;
    private $columns_line = 0 ;

    public function __construct( $n='default' )
    {
        $this->name = $n ; 
    }

    public function addColumn( $title = "N/A" , $l=null )
    {
        if ($l!=null || $l == 0 )
            $line = ( ($l-1) <= $this->columns_line )? $l : $this->columns_line;
        else
            $line = $this->columns_line;
        if ($line>$this->columns_line)
            $this->columns_line = $line ;
    
        $col = new Column( $title , $line , $this );
        array_push( $this->columns , $col );
        return $col;
    }
    
    public function addItem( $content = "N/A" , $p_c )
    {   
        $item = new Item( $content , $p_c , $this , count($this->items) );
        array_push( $this->items , $item );
        return $item;
    }

    public function setItemRow( $row , $num )
    {
        $max = count($this->items) ;
        if (($num+1)>$max) return false ;
        $temp = $this->items[$num] ;
        $temp_row = $row->row ;
        $this->items[$num] = $this->items[$row->row] ;
        $this->items[$row->row] = $temp ;
        $this->items[$num]->row = $num ;
        $this->items[$row->row]->row = $temp_row ;
        unset($temp);
        unset($temp_row);
        return true;
    }

    public function deleteItemFromTable( $item )
    {
        $key = array_search( $item , $this->items ) ;
        if( $key )
        {
            unset($this->items[$key]);
            $this->items = array_values($this->items) ;
            return true;
        }
    }
    public function deleteColumnFromTable( $col )
    {
        $key = array_search( $col , $this->columns ) ;
        if( $key )
        {
            unset($this->columns[$key]);
            $this->columns = array_values($this->columns) ;
            return true;
        }
    }

    private function getColumnsByLine($l)
    {
        $arr = [] ;

        for ($k = 0 ; $k < count($this->columns) ; $k++ )
        {
            if($this->columns[$k]->getLineNumber() == $l )
            array_push($arr , $this->columns[$k] );
        }

        return $arr ;

    }

    private function getItemsByColumn($col)
    {
        $arr = [] ;

        for ($k = 0 ; $k < count($this->items) ; $k++ )
        {
            if($this->items[$k]->getColumn() == $col )
            array_push($arr , $this->items[$k] );
        }

        return $arr ;

    }

    public function display()
    {
        echo "
        <style>
        /* Center tables for demo */
        table {
          margin: 0 auto;
        }
        
        /* Default Table Style */
        table {
          color: #333;
          background: white;
          border: 1px solid grey;
          font-size: 12pt;
          border-collapse: collapse;
        }
        table thead th,
        table tfoot th {
          color: #777;
          background: rgba(0,0,0,.1);
        }
        table caption {
          padding:.5em;
        }
        table th,
        table td {
          padding: .5em;
          border: 1px solid lightgrey;
        }
        /* Zebra Table Style */
        [data-table-theme*=zebra] tbody tr:nth-of-type(odd) {
          background: rgba(0,0,0,.05);
        }
        [data-table-theme*=zebra][data-table-theme*=dark] tbody tr:nth-of-type(odd) {
          background: rgba(255,255,255,.05);
        }
        /* Dark Style */
        [data-table-theme*=dark] {
          color: #ddd;
          background: #333;
          font-size: 12pt;
          border-collapse: collapse;
        }
        [data-table-theme*=dark] thead th,
        [data-table-theme*=dark] tfoot th {
          color: #aaa;
          background: rgba(0255,255,255,.15);
        }
        [data-table-theme*=dark] caption {
          padding:.5em;
        }
        [data-table-theme*=dark] th,
        [data-table-theme*=dark] td {
          padding: .5em;
          border: 1px solid grey;
        }
        </style>
        <table style='width:100%'>
        <caption>
          ".$this->name."
        </caption>
        ";
            
            if ( $this->columns_line>0 ){       
                  
                echo "<thead>";
                for($k = 0 ; $k <= $this->columns_line ; $k++ )
                {
                    $el = $this->getColumnsByLine( $k ) ; 
                    
                    echo "<tr>";                
                    foreach( $el as $v )
                    {
                        echo "<th>".$v->getTitle()."</th>" ;
                    }
                    echo "</tr><tr>";
                    
                    foreach( $el as $v )
                    {
                        foreach( $this->getItemsByColumn( $v ) as $v )
                        {
                            echo "<td>".$v->getContent()."</td>";
                        }  
                    }  

                    echo "
                    </tr>
                    ";
                }
                echo "</thead>";
            }else{
                    echo"
                    <thead>
                    <tr>";
                    
                    foreach($this->getColumnsByLine( 0 ) as $v )
                    {
                        echo "                    
                        <th>".$v->getTitle()."</th>
                        ";
                    }
                    
                    echo "
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    ";
                    $counter = 0;

                    foreach($this->items as $key=>$v )
                    {       
                            if(($counter+1)>count($this->columns))
                            {
                                echo "<tr>";
                                $counter = 0;
                            }                            
                            echo"<td>".$v->getContent()."</td>";
                            if( $counter == (count($this->columns)-1) )
                                echo"</tr>";
                            $counter++;

                    }
                    echo "
                    </tbody>";
                
            }
         echo "</table>";
    }

    function __destruct()
    {
        for ($k = 0 ; $k < count($this->items) ; $k++ )
        {
            unset( $this->items[$k] ) ;
        }
        
        for ($k = 0 ; $k < count($this->columns) ; $k++ )
        {
            unset( $this->columns[$k] ) ;
        }
    }
    
}
