PROGRAM SarahRevereCGI;
USES 
  DOS;
VAR
  Query: STRING;
BEGIN 
  WRITELN('Content-Type: text/plain; charset=utf-8');
  WRITELN;
  Query := GetEnv('QUERY_STRING');
  IF Query = 'lanterns=1' 
  THEN
    WRITELN('The British are coming by land.')
  ELSE
    IF Query = 'lanterns=2' 
    THEN
      WRITELN('The British are coming by sea.')
    ELSE
      WRITELN('Sarah didn''t say')
END.
