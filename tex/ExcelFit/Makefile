all: pdf

pdf: 2016-ExcelFIT-php2cpp.tex
	pdflatex 2016-ExcelFIT-php2cpp.tex
	bibtex 2016-ExcelFIT-php2cpp.aux
	pdflatex 2016-ExcelFIT-php2cpp.tex
	pdflatex 2016-ExcelFIT-php2cpp.tex
	#dvips 2016-ExcelFIT-php2cpp.dvi
	#ps2pdf 2016-ExcelFIT-php2cpp.ps

	rm 2016-ExcelFIT-php2cpp.aux
	#rm 2016-ExcelFIT-php2cpp.dvi
	#rm 2016-ExcelFIT-php2cpp.ps
	rm 2016-ExcelFIT-php2cpp.blg
	rm 2016-ExcelFIT-php2cpp.bbl
	rm 2016-ExcelFIT-php2cpp.log
	rm 2016-ExcelFIT-php2cpp.out
	#rm 2016-ExcelFIT-php2cpp.toc
