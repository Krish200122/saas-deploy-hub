extract_base_name() {
  echo "$1" | sed 's/_\(api\|fullapp\)//'
}

map_source_db() {
  local base="$1"
  if [[ "$base" == *"erp"* ]]; then
    echo "GENIE_ERP_DB"
  elif [[ "$base" == *"leathers"* ]]; then
    echo "Ecommerce_Leathers"
  else
    echo ""
  fi
}

find_available_port() {
  local base_port=7000
  local max_port=7300
  while [ $base_port -le $max_port ]; do
    if ! lsof -i :$base_port &>/dev/null; then
      echo $base_port
      return
    fi
    ((base_port++))
  done
  echo ""
}
