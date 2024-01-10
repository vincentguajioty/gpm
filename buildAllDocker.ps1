param([string]$version)

cd backend

$command = "docker image build . -t gpm-bo:"+$version
Invoke-Expression $command

cd ..
cd frontend

$command = "docker image build . -f Dockerfile -t gpm-fo:"+$version
Invoke-Expression $command

cd ..

$command = "docker save gpm-bo:"+$version+" -o gpm-bo-"+$version+".tar"
Invoke-Expression $command
$command = "docker save gpm-fo:"+$version+" -o gpm-fo-"+$version+".tar"
Invoke-Expression $command

$command = "docker rmi gpm-bo:"+$version
Invoke-Expression $command
$command = "docker rmi gpm-fo:"+$version
Invoke-Expression $command